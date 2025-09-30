🔒 Dependency & Change Safety Playbook (Optimized)

Golden Rule: Analyze dependencies first, code second.

1) Core Checklist (Use Every Time)

Impact & Risk

Which systems/components share this code?

What existing functionality could break?

Downstream effects (jobs, queues, monitoring, users, timeframes)?

Risk level: HIGH (core logic) / MEDIUM (shared service) / LOW (isolated UI)

Testing Strategy

What must be retested post-change (features, flows, jobs)?

How to verify no regressions?

Manual scenarios + rollback plan

Go/No-Go

Are there time-sensitive ops running?

Any in-progress user workflows?

Do I have a safe deploy window?

2) Think in Dependencies

Ask before changing:

Who shares this code? (same job class, service, DB tables, endpoints, multiple timeframes/users)

What could break? (running jobs/monitoring, auth/roles, queued requests)

How do I test safely? (isolation first, then full path; clear rollback)

Red Flags (Stop & Re-assess)

Core job classes

DB schema changes

Shared services

Auth/session/CSRF

Queue/cron processing

3) TickerAI-Specific Notes

Timeframes (1h vs 1d)

Shared: GenerateStockAdvice.php, YahooFinanceService.php, PriceMonitoringService.php, requests table

Safe pattern: branch by $request->timeframe (e.g., 120s vs 300s), test both after any change.

Monitoring

MonitorStockPrices.php, PriceMonitoringService.php, cron, queue workers

Don’t restart services during market hours; timeout handling affects all active requests.

Authentication

Sessions, middleware, roles/permissions, CSRF

Password reset, role changes, session tweaks have wide blast radius.

4) Safe Development Workflow

Analyze → Minimal Change → Isolate/Test → Safe Deploy

Analyze: map dependencies, set risk, plan tests

Minimal changes: smallest diff; verify after each step

Isolate & test: new path, then regression; cover edge/error cases; document

Deploy safely: clear commit message, low-traffic window, monitor, rollback ready

5) Common Scenarios (Right vs Wrong)

Fix 1d timeframe

❌ Global timeout change

✅ Conditional by timeframe; verify 1h still OK

Add monitoring feature

❌ Edit existing service directly; ad-hoc schema change

✅ New command/service; migrations; test with real requests

Fix email delivery

❌ Tweak global mail config; touch auth flow

✅ Scope to reset emails via dedicated queue job; verify other mails

6) One-Page Quick Checklist
□ Files I’m changing + who uses them
□ Shared codepaths (jobs/services/endpoints/tables/timeframes)
□ What can break & how I’ll detect it
□ Exact regression tests to run
□ Rollback plan & deploy window
□ Time-sensitive ops / in-progress workflows considered


If anything is unclear → stop and analyze.

7) Incident Postmortem (Condensed)

Issue: Production UI broke (65 assets / 10,844 LOC missing)
Date: Sep 14, 2025
Root Cause: Windows + Git core.ignorecase=true → case-only renames weren’t tracked; folders deleted in repo; prod git pull removed assets.

Key Signals

Unstyled HTML; 404 on non-Admin assets (Dashboard/, Settings/, Market/, Users/, Requests/, Home/)

Local assets existed; prod missing

Fix

git config core.ignorecase false
git add -A
git commit -m "Restore missing assets; fix case-sensitivity tracking"
# deploy: git pull on server, then clear Laravel caches
php artisan cache:clear && php artisan config:clear && php artisan view:clear


Verification

HTTP 200 for all CSS/JS

Visual/UI checks (home, signin/signup, dashboard, settings, market, users, requests)

Prevention

Enforce core.ignorecase=false on Windows dev

Consistent folder casing from day one

Pre-deploy asset availability checks (curl -I …)

Lightweight UI smoke test (Puppeteer) in CI

8) Production Issues & Resolutions (Summaries)

Settings: Update Profile Didn’t Persist

Cause: Form action pointed to /settings/ (trailing slash mismatch) vs route settings.update

Fix: Use action="{{ route('settings.update') }}" (minimal, safe)

Requests: “New Request” Button Scheduling

Need: Only clickable during IDX hours (WIB)

Fix: Server-rendered check (Jakarta tz) with disabled state + schedule notice outside hours

Super Admin Login Failed

Cause: Account existed locally, not in production; later, password hash update issues via direct SQL

Fix: Create/verify on prod; set password via Laravel (Hash) to ensure valid bcrypt hash

User Update (500/504) & User Delete (Timeout)

Causes:

Mass updates of historical requests on profile change

Email notifications (SMTP) during updates/deletes

No FK cascade or N+1 deletes via ORM for large requests sets

Fixes:

Disable historical backfill; only new requests use updated profile

Remove synchronous email notifications on these paths

Prefer DB-level cascade (FK) or single raw SQL deletes:

DB::transaction(function () use ($userId) {
  DB::delete('DELETE FROM requests WHERE user_id = ?', [$userId]);
  DB::delete('DELETE FROM users   WHERE id = ?',       [$userId]);
});


Result: user delete in milliseconds; updates no longer timeout

9) Market Hours Logic (Reference – Asia/Jakarta)

Mon–Thu: 09:00:00–12:00:00, 13:30:00–15:49:59

Fri: 09:00:00–11:30:00, 14:00:00–15:49:59
Outside windows → disable “New Request” and show schedule notice.

10) Success Criteria

Identify shared components before coding

Minimal, targeted changes

Regression tests pass (critical paths and timeframes)

User confirms existing flows still work

No regressions in logs/metrics post-deploy

11) Daily Ritual (Ultra-Short)

Read this page (2 min)

Write dependency notes for today’s change (what shares it / what could break / tests)

Execute minimal change + verify stepwise

Deploy in safe window + monitor + have rollback

TL;DR

Default to safety: map dependencies → smallest change → isolate + test → careful deploy.
Never assume; always verify on the actual environment.