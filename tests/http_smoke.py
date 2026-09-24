"""Run only against a disposable local Byte Bazaar database.
Usage: BYTE_BAZAAR_ALLOW_TEST_WRITES=1 python3 tests/http_smoke.py http://127.0.0.1:8088
"""
import base64, http.cookiejar, json, os, re, sys, time
import urllib.request, urllib.parse, urllib.error
assert os.environ.get('BYTE_BAZAAR_ALLOW_TEST_WRITES') == '1', 'Explicit disposable-test opt-in required'
base = sys.argv[1].rstrip('/')
assert urllib.parse.urlparse(base).hostname in ('127.0.0.1', 'localhost'), 'Only loopback is allowed'
checks = 0

def check(value, message):
    global checks
    assert value, message
    checks += 1

class Client:
    def __init__(self):
        self.jar = http.cookiejar.CookieJar()
        self.opener = urllib.request.build_opener(urllib.request.HTTPCookieProcessor(self.jar))
        self.token = None
    def request(self, path, data=None, expected=200, raw=False):
        headers={'Accept':'application/json'} if data is not None else {}
        req=urllib.request.Request(base+path, urllib.parse.urlencode(data).encode() if data is not None else None, headers)
        try:
            response=self.opener.open(req)
        except urllib.error.HTTPError as error:
            response=error
        body=response.read().decode()
        check(response.status == expected, f'{path}: expected {expected}, got {response.status}: {body[:250]}')
        if 'text/html' in response.headers.get('Content-Type',''):
            match=re.search(r'name="csrf-token"\s+content="([^"]+)"',body)
            if match:self.token=match.group(1)
        if raw:return body,response.headers
        return json.loads(body) if 'application/json' in response.headers.get('Content-Type','') else body
    def post(self,path,data=None,expected=200):
        return self.request(path,dict(data or {},csrf_token=self.token),expected)
    def multipart(self,path,fields,filename,content,mime,expected):
        boundary='byte-boundary-849123'
        body=b''
        for key,value in dict(fields,csrf_token=self.token).items():
            body+=(f'--{boundary}\r\nContent-Disposition: form-data; name="{key}"\r\n\r\n{value}\r\n').encode()
        body+=(f'--{boundary}\r\nContent-Disposition: form-data; name="image"; filename="{filename}"\r\nContent-Type: {mime}\r\n\r\n').encode()+content+f'\r\n--{boundary}--\r\n'.encode()
        req=urllib.request.Request(base+path,body,{'Accept':'application/json','Content-Type':f'multipart/form-data; boundary={boundary}'})
        try:response=self.opener.open(req)
        except urllib.error.HTTPError as error:response=error
        output=response.read().decode();check(response.status==expected,f'Upload: {response.status} {output}')
        return json.loads(output)

alice,bob,admin=Client(),Client(),Client()
html,headers=alice.request('/',raw=True)
bob.request('/')
check('HttpOnly' in headers.get('Set-Cookie','') and 'SameSite=Lax' in headers.get('Set-Cookie',''),'Cookie protection missing')
check('no-store' in headers.get('Cache-Control',''),'Session response can be cached')
check(alice.token!=bob.token,'Guest CSRF tokens mixed')
check(alice.request('/products?categories=bad',expected=422),'Invalid filter accepted')
alice.request('/cart/add/1',{'qty':1},403)
alice.post('/cart/add/1',{'qty':1})
cart=alice.request('/api/cart')['items'];item=cart[0]['id']
check(bob.request('/api/cart')['items']==[],'Guest carts mixed')
bob.post('/cart/update',{'id':item,'qty':2},404)
bob.post(f'/cart/remove/{item}',{},404)
alice.post('/cart/update',{'id':item,'qty':-1},422)
alice.post('/cart/add/1',{'qty':999},422)
stamp=str(time.time_ns());password='isolated-test-password'
for client,name in [(alice,'Alice'),(bob,'Bob')]:
    client.request('/register')
    old_cookie=[c.value for c in client.jar]
    client.post('/register',{'name':name,'email':name.lower()+stamp+'@example.test','password':password,'c-password':password})
    client.request('/user/dashboard')
    check(old_cookie!=[c.value for c in client.jar],'Session ID was not rotated at login')
check(len(alice.request('/api/cart')['items'])==1,'Guest cart did not merge on signup')
check(bob.request('/api/cart')['items']==[],'Account carts mixed')
bob.request('/admin',expected=403)
checkout=alice.request('/checkout');key=re.search(r'name="checkout_key"\s+value="([^"]+)"',checkout).group(1)
address={'customer_name':'Alice','customer_email':'alice@example.test','shipping_address':'<script>alert("x")</script>','shipping_city':'Toronto','shipping_country':'Canada','shipping_postal_code':'A1A 1A1','phone':'5550100123','payment_method':'cash_on_delivery','checkout_key':key}
order=alice.post('/checkout',address)['order_id']
check(alice.post('/checkout',address)['order_id']==order,'Duplicate order created on retry')
check(alice.request('/api/cart')['items']==[],'Checkout did not clear the cart')
page=alice.request(f'/order/{order}')
check('&lt;script&gt;' in page and '<script>alert(' not in page,'Stored customer data was not escaped')
bob.request(f'/user/order/{order}',expected=404)
bob.request(f'/order/{order}',expected=404)
alice.post('/cart/add/2',{'qty':1})
alice.post('/logout')
check(alice.request('/api/cart')['items']==[],'Logout retained account cart')
alice.request('/login');alice.post('/login',{'email':'alice'+stamp+'@example.test','password':password});alice.request('/user/dashboard')
check(len(alice.request('/api/cart')['items'])==1,'Account cart did not persist across login')
admin.request('/login');admin.post('/login',{'email':'admin@example.test','password':'local-demo-admin-only'});admin.request('/admin')
for path in ['/admin/products','/admin/products/create','/admin/categories','/admin/categories/create','/admin/users','/admin/orders',f'/admin/order/{order}','/orders','/account']:
    admin.request(path)
admin.post(f'/admin/order/{order}/update-status',{'status':'delivered'},409)
admin.post(f'/admin/order/{order}/update-status',{'status':'processing'})
check('Processing' in admin.request(f'/admin/order/{order}'),'Order status not updated')
fields={'name':'Upload check '+stamp,'description':'A synthetic test product','price':'12.34','stock':'2','category_id':'1'}
admin.multipart('/admin/products/create',fields,'script.php',b'<?php echo "bad";','image/png',422)
png=base64.b64decode('iVBORw0KGgoAAAANSUhEUgAAAAEAAAABCAQAAAC1HAwCAAAAC0lEQVR42mP8/x8AAwMCAO+aZ1sAAAAASUVORK5CYII=')
admin.multipart('/admin/products/create',fields,'script.php',png,'image/png',200)
page=admin.request('/products?q='+urllib.parse.quote(fields['name']))
check(re.search(r'/uploads/products/[a-f0-9]{48}\.png',page),'Upload extension was not derived from file content')
print(f'HTTP smoke checks passed: {checks}. Two independent customers plus admin; cookies, CSRF, ownership, login/logout, checkout retry, XSS, routes, status, and uploads.')
