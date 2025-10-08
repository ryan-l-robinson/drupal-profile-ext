This module extends from [the contrib profile module](https://www.drupal.org/project/profile) module, improving two components:

1. Alters the label that gets displayed to use the name, based on fields on the profile.
2. Fixes [linkit](https://www.drupal.org/project/linkit) filtering to get accurate results based on matches to any of the first name, last name, department, and job title field.

For these features to provide any benefit, the profile type would need the matching field names, but it is set up in such a way that it should default back to default behaviour if those fields do not exist.
