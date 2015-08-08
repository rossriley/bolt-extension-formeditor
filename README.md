### Bolt Forms Editor

### Experimental warning - read details below

This is a small extension that builds on top of the `bolt/boltforms` extension to provide
a very basic UI for editors to use without needing to edit a YML file.

Functionality supported so far:

1. Creating a new form 
2. Listing current forms 
3. Editing selected settings for notification / feedback
4. Listing and sorting current fields.
5. Deleting current fields.
6. Adding new fields (with limited options)

The feature set offered via UI is small in comparison to the available options, but the
extension only writes to the fields that it has UI for. So for example an editor can change
the label of a setup form field but the constraints / validations will stay in place.

### Experimental Warning

This extension is new and has not yet been tested by a large audience, and as such it may carry a few risks.

It is recommended that you keep a backup of any current `app/config/extensions/boltforms.bolt.yml` 
files during the evaluation of this.

Note that this extension will only work on **PHP 5.4 or later**.