# Changelog

Release notes for this Moodle line, newest first. The release workflow publishes the
first `## ` section to the Moodle Marketplace, so every version bump PR adds one on top:
`## <release> (<version>)`, with both values copied from version.php.

Write for a Moodle admin deciding whether to upgrade: what changed, and whether anything
needs doing. Example, indented so the workflow ignores it (write yours without the indent):

    ## 5.2.3 (2026110200)

    ### Fixed
    - AB#nnnnnn: Ally feedback icons on images inside tables sit next to the image again.

    ### Changed
    - AB#nnnnnn: <one line on behaviour an admin or teacher will notice>

    ### Upgrade notes
    - No database changes and no new settings.

## 5.3.1 (2026110301)

### Added
- The institution and course accessibility reports launch over LTI 1.3 when their Ally tool is registered in Moodle and active; otherwise they launch over LTI 1.1, as before.

### Upgrade notes
- Requires tool_ally 5.3.1. No database changes and no new settings.

## 5.3.0 (2026110300)

### Added
- Support for Moodle 5.3. This version requires Moodle 5.3.

### Upgrade notes
- Requires Moodle 5.3. No database changes and no new settings.
