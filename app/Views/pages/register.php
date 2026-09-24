<section class="shell auth-layout">
  <div class="auth-story">
    <span class="eyebrow light">YOUR OWN LITTLE CORNER OF TECH</span>
    <h1>
      A little more
      <br />
      you.
      <br />
      <em>
        A lot more
        <br />
        possibility.
      </em>
    </h1>
    <p>Keep your favorite upgrades close and your orders in one place.</p>
    <span class="auth-decoration" aria-hidden="true">b.</span>
  </div>
  <div class="auth-card">
    <span class="eyebrow">JOIN BYTE BAZAAR</span>
    <h2>Make yourself at home.</h2>
    <p>Your next chapter starts with an account.</p>
    <form action="<?= e(
    url_path("register"),
) ?>" method="post" data-async>
      <?= csrf_field() ?>
      <div class="field">
        <label for="name">Your name</label>
        <input id="name" name="name" autocomplete="name" maxlength="100" required />
      </div>
      <div class="field">
        <label for="email">Email address</label>
        <input
          id="email"
          type="email"
          name="email"
          autocomplete="email"
          maxlength="255"
          placeholder="you@example.com"
          required
        />
      </div>
      <div class="field">
        <label for="password">Password</label>
        <div class="password-input">
          <input
            id="password"
            type="password"
            name="password"
            autocomplete="new-password"
            minlength="12"
            maxlength="72"
            required
          />
          <button
            type="button"
            class="text-button"
            data-password="password"
            aria-label="Show password"
          >
            Show
          </button>
        </div>
        <small>Use 12–72 characters. A memorable phrase works well.</small>
      </div>
      <div class="field">
        <label for="confirm-password">Confirm password</label>
        <input
          id="confirm-password"
          type="password"
          name="c-password"
          autocomplete="new-password"
          minlength="12"
          maxlength="72"
          required
        />
      </div>
      <p class="form-error" role="alert" hidden></p>
      <button class="button orange full" type="submit">Create account ↗</button>
    </form>
    <p class="auth-switch">
      Already at home here?
      <a href="<?= e(
    url_path("login"),
) ?>">Sign in</a>
    </p>
    <a class="text-link" href="<?= e(
    url_path("products"),
) ?>">← Back to exploring</a>
  </div>
</section>
