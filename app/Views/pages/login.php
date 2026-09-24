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
    <span class="eyebrow">WELCOME BACK</span>
    <h2>Good to see you again.</h2>
    <p>Sign in to pick up where you left off.</p>
    <form action="<?= e(
    url_path("login"),
) ?>" method="post" data-async>
      <?= csrf_field() ?>
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
            autocomplete="current-password"
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
      </div>
      <p class="form-error" role="alert" hidden></p>
      <button class="button orange full" type="submit">Sign in ↗</button>
    </form>
    <p class="auth-switch">
      New around here?
      <a href="<?= e(
    url_path("register"),
) ?>">Create an account</a>
    </p>
    <a class="text-link" href="<?= e(
    url_path("products"),
) ?>">← Back to exploring</a>
  </div>
</section>
