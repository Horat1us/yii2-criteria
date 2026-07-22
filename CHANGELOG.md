# Yii2 Criteria Changelog

### 1.1.0
- Update PHP 7.4
- Support for PHP 8.0
- Integrate CodeSniffer
- Reformat code. Add strict properties types.
- Tests for PaginationCriteria, SelectCriteria
- **SelectCriteria**: resolve fields exclusively through a new `selectKeys` server-defined
  whitelist, matching `SortCriteria::$sortKeys` / `SearchCriteria::$searchKeys`. Previously,
  fields were accepted from client input as-is (checked only against `ActiveRecord::attributes()`
  for `ActiveQuery`, unchecked for plain `Query`), which broke on ambiguous columns across joined
  tables and let a client-supplied table qualifier reach SQL unvalidated. `selectKeys` also
  supports aliasing a field to a pre-authored expression, like `sortKeys`/`searchKeys` already do.
  **Breaking**: consumers must now set `selectKeys` (or a subclass must override `getSelectKeys()`)
  for any field to be selected; without it, `apply()` drops every field, and an empty select is
  treated by Yii as `SELECT *`.

### 1.0.3
- Add [SelectCriteria](./src/SelectCriteria.php)