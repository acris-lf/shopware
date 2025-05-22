---
title: Implemented mt-empty-stage component
---
# Administration
* Added wrapper around the new mt-empty-state component
___
# Next Major Version Changes
## Removal of "sw-empty-state"
* The old "sw-empty-state" component will be removed in the next major version. Please use the new "mt-empty-state" component instead.

Before:
```html
<sw-empty-state title="short title" subline="longer subline" />
```
After:
```html
<mt-empty-state title="short title" description="longer description"/>
```