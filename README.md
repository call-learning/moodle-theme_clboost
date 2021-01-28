CALL Learning Boost Based theme
==

[![Build Status](https://travis-ci.org/call-learning/moodle-theme_clboost.svg?branch=master)](https://travis-ci.org/call-learning/moodle-theme_clboost)

This theme is intended to be used as a parent theme. It brings additional utilities
and fixes from the standard boost theme. 


Logos
==

Logos can be found in the theme directly in the pix folder (two versions logo.png and logo-compact.png)

Foldable navigations menu
==

The boost navigation menu has 2 stages :
1. Just show icons
2. Fully unfolded

The navigation stays responsive and disappear completely on small screen when not needed.

Menus that are present in the header will be displayed at the top as foldable items.

Branding
==

Additional branding can be added throught the use of scss variable.
For example if a setting in the theme starts with branding_xxxx (for example branding_primary), then
the xxxx variable will be created and assigned.

For example if branding_primary is set to #0A0A0A, then a variable named:

    $primary: #0A0A0A

Will be appended at the top of the scss file.

Templates
==

We usually look for templates in the following order (component = block_myoverview for example).

1. '/theme/clboost/templates/block_myoverview/',
2. '/theme/boost/templates/block_myoverview/',
3. '/blocks/myoverview/templates/'

But if the theme clboost has a child, this means that the list will be the following:

1. '/theme/mytheme/templates/block_myoverview/',
2. '/theme/theme/templates/block_myoverview/',
3. '/blocks/myoverview/templates/'
 
In this case it means that usual boost overrides (columns2.mustache, ...) will not be found.
The new mustache template finder will always add the parent theme root folder to the end of the list so the templates
can be overriden and we do not need to repeat them in the clboost theme.

Google Analytics
==

* Google analytics code can be inserted into the page header. The settings will
also appear in the subthemes.
They will only be enabled the user has accepted the cookies policy (see tools_policy).
   
TODO
==


Features
==

* Foldable navbar
* Flexible renderers (overridable by child theme more easily)
* Developper tools: boostwatch preview and element library 