**Dept Autologin**

The module can be installed either usimg drush or through the User Inteface.

`drush en dept_autologin`

Tokens will be created and added to user accounts during the install.

**Middleware**

This is an interesting test. I have experience with middleware from my Nokia project. There it was used
for intercepting the requests and extracting the domain name from the Akamai requests (X_FORWARED_FOR_HEADER)
so the correct domain name could be used by modules to generate urls. For example sitemap urls. Drupal CORS
integration, Ban module and Session services make use of middleware.

**Configuration API**

As ever with Drupal there are many ways to do one thing. Importing fields using yml files is a solution but
it has its limitations in this situation where its not possible to set the display options on entity_form or
entity_view as you would be overwriting existing config so Drupal prevents import of these files.That said
in a CI process it would be typical to overwrite these files in the config export directory and export the
created fields rather than creating them in a module.

An alternative approach would be to use the Field API to create the fields at inatall.

I have also added a Constraint Validator to the AUTH TOKEN field, so it is not possible to enter a value that is
shorter than 32 characters. This could be extended to ensure the value entered is numeric.








