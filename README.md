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

The boost navigation menu has 3 stages :
1. Fully collapsed
2. Just show icons
3. Fully unfolded


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

TODO
==


Features
==

* Foldable navbar
* Flexible renderers (overridable by child theme more easily)
* Developper tools: boostwatch preview and element library 