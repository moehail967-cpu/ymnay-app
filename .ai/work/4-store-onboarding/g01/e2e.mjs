// QA-only wrapper: run the original engineer browser suite unchanged, then independent UI acceptance.
// Original engineer-e2e.mjs Git blob: f512420d2a6cf166047ca9959394530f0c7a2b08.
await import('./engineer-e2e.mjs');
await import('./salem-ui-review.mjs');
