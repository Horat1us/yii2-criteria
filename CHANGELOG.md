# Yii2 Criteria Changelog

### 2.0.0 (provisional, not yet released)
This version is not cut yet - it's staged on top of 1.1.0's `feature/select-criteria-keys` branch
pending a decision on when/whether to release it and how consumers migrate. Listed separately
from 1.1.0 because, unlike that release, this one changes runtime behavior for existing consumers
without any code-level signal (no new required constructor argument, nothing PHP itself would
flag) - semver rules that out as a minor/patch bump.
- **BREAKING - SelectCriteria**: resolve fields exclusively through a new `selectKeys`
  server-defined whitelist, matching `SortCriteria::$sortKeys` / `SearchCriteria::$searchKeys`.
  Previously, fields were accepted from client input as-is (checked only against
  `ActiveRecord::attributes()` for `ActiveQuery`, unchecked for plain `Query`), which broke on
  ambiguous columns across joined tables and let a client-supplied table qualifier reach SQL
  unvalidated. `selectKeys` also supports aliasing a field to a pre-authored expression, like
  `sortKeys`/`searchKeys` already do.
  Consumers must set `selectKeys` (or override `getSelectKeys()`) for any field to be selected;
  without it, `apply()` drops every field, and Yii2 treats the resulting empty select as
  `SELECT *` - silently more permissive than before, not less. **Every existing consumer of plain
  `SelectCriteria::class` must be audited and updated before adopting this version.**

### 1.1.0
- Update PHP 7.4
- Support for PHP 8.0
- Integrate CodeSniffer
- Reformat code. Add strict properties types.
- Tests for PaginationCriteria, SelectCriteria

### 1.0.3
- Add [SelectCriteria](./src/SelectCriteria.php)