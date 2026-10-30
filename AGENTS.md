# AGENTS.md: Ally for Moodle

Everything above `## This repo` is identical in moodle-tool_ally, moodle-filter_ally and moodle-report_allylti, on `master` and every supported `MOODLE_*_STABLE` branch. Change it in all of them together. Copilot code review reads only the first 4,000 characters, from the pull request's base branch, so the version rules come first.

## Version rules

Moodle offers any higher `$plugin->version` as an update and refuses to install a lower one over a higher one. A wrong number strands sites, and a later release can't undo it. In review, a break of any rule below is a blocking issue.

| Branch | Moodle | Frozen date |
| --- | --- | --- |
| `MOODLE_502_STABLE` | 5.2 | `20261102` |
| `MOODLE_503_STABLE` | 5.3 | `20261103` |

`MOODLE_501_STABLE` and older are no longer supported: no version bumps, no tags. `master` is not a release line.

1. **Version = frozen date + two digits.** Each release adds 1 to the last two digits: `2026110200`, `2026110201`, up to `99`. Never change the date and never use today's date: that let an older line outrank a newer one (filter_ally issue 109). The version line says so: `// 20261102 = frozen line date YYYYMMDD - do not modify!` This follows https://moodledev.io/docs/apis/commonfiles/version.php.
2. **A new line** gets a date later than every existing line. The first commit on its branch sets version.php to `<date>00` with that comment and `supported`.
3. **`$plugin->supported = [<line>, <line>]`**. The Marketplace lists the version under these Moodle versions only.
4. **`$plugin->release`** changes with every tagged version (`5.2.3` → `5.2.4`). A version number that was published is never reused.
5. **`$plugin->dependencies`** never exceed what the sibling plugin has released on the same line.
6. **`$plugin->requires`** stays as it is in a version bump. Change it only in its own pull request that says why.

Before you push a version.php change, run `.github/scripts/check-version-order.sh <line>`. The `Version order` workflow runs the same check on every pull request, and a `FAIL` blocks the merge.

## Branches

- A fix that isn't specific to one Moodle version goes to `master` first. Then `git cherry-pick -x` it onto each supported `MOODLE_*_STABLE` branch: one pull request per branch, each with its own version bump. A fix for one Moodle version only goes straight to that branch.
- Never copy version.php between branches. The date, `supported`, `requires` and dependency minimums differ per line, so a cherry-pick that touches version.php needs fixing by hand.
- filter_ally's CI checks out the branch with the same name in tool_ally and report_allylti, or else that line's `MOODLE_<line>_STABLE`. For a change across repos, push the branches to tool_ally and report_allylti first.

## Releasing

A release publishes to the public Moodle Marketplace and can't be withdrawn. Releases come only from `MOODLE_<line>_STABLE`. An agent asked to release does every step below, one line at a time, and **stops for the person's explicit go-ahead before pushing each tag**.

1. **What ships.** `git fetch origin --tags`, then `git log --oneline <last AY-<line>-R tag>..origin/MOODLE_<line>_STABLE`. Nothing listed: nothing to release. Release a sibling plugin too only when a dependency minimum moves.
2. **Bump** in a pull request into `MOODLE_<line>_STABLE`: `$plugin->version` +1 (rule 1) and `$plugin->release` +1 in its last part (`5.2.3` → `5.2.4`). Nothing else changes in version.php.
3. **Before tagging**, on the merged tip `<sha>`:
   - `.github/scripts/check-version-order.sh <line>` prints `OK`.
   - CI passed on `<sha>`, or `<sha>` has the same tree (`git rev-parse <sha>^{tree}`) as a pull request head whose CI passed. Only filter_ally runs tests in CI.
   - The sibling versions that `dependencies` need are on the Marketplace.
   - The next unused tag number: `git ls-remote --tags origin 'AY-<line>-*'`.
4. **Tag**, after the go-ahead: `git tag AY-<line>-R<n> <sha>`, then `git push origin AY-<line>-R<n>`. `<sha>` must still be the branch tip.
5. **Watch the run**: `gh run list --workflow moodle-release.yml --branch AY-<line>-R<n>`, then `gh run watch <id> --exit-status`. Success is `OK — pushed from MOODLE_<line>_STABLE` from `guard`, and `Response HTTP Status: 201` from the upload, with `version`, `releaseName` and `moodleVersions` matching version.php.
6. **Confirm** about 10 minutes later on `https://marketplace.moodle.com/plugins/<component>/versions`: the line shows `<release> (<version>)`. Only then is it released.

If it fails:
- `guard` fails: the tag is not on a stable branch tip. Delete it (`git push origin :refs/tags/<tag>`, `git tag -d <tag>`) and tag the right commit.
- The upload is not `201`: the log prints the response body. `401` or `403` means `MOODLE_MARKETPLACE_TOKEN` is missing or expired, and a person must replace it.
- Never re-run a job that got `201`, and never delete a published tag.

## This repo: report_allylti

A report plugin that opens Ally's accessibility report with an LTI 1.1 launch. It stores and shows no data of its own. It installs at `public/report/allylti`.

### Commands

Run from the Moodle root:

```bash
vendor/bin/phpunit --group report_allylti
php public/admin/tool/behat/cli/run.php --tags=@report_allylti
cd public/report/allylti && npx grunt amd     # amd/build is committed
```

This repo has no test CI: filter_ally's CI runs these tests. Tag new tests `@group report_allylti` and `@group ally`, and new features `@report @report_allylti @suite_ally`, or they drop out of the CI runs.

### How it works

- **Configuration lives in `tool_ally`.** The plugin counts as unconfigured unless `adminurl`, `key` and `secret` are all set. That check is repeated in `settings.php`, `lib.php` and `classes/local/launch_config.php`; keep the three in step.
- **Launch.** `view.php` (iframe, new window or redirect) → `launch.php`, which builds a throwaway LTI instance (`id => 0`, `typeid => null`) and calls core's `lti_launch_tool()`. Without a `typeid`, Moodle sends an LTI 1.0/1.1 launch signed with the key and secret.
- **Course report.** It reuses the admin URL, and `launch_config::get_url()` rewrites a trailing `institution` to `instructor`. Changing the shape of the admin URL breaks the course report without any error.
- **Capabilities.** `report/allylti:viewadminreport` at system level, `report/allylti:viewcoursereport` at course level. For the course report, `launch.php` checks only `require_login($course)`.
- `lib.php` adds the course navigation node twice on purpose, once for flat navigation and once for classic themes.
- `$CFG->report_allylti_launch_container` in `config.php` picks the launch container (default: embed). `view.php` copies code from `mod/lti/view.php`, including a YUI resize block.
- `view.php` and `settings.php` call `mr_off()`, an Open LMS feature flag. Keep the `$undertest` guard around it.
- `db/hooks.php` loads `amd/src/main.js` on every page to open course report links in a new tab, except under Behat.
- In Behat, `tests/fixtures/report.php` stands in for Ally.
