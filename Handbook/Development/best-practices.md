This document describes our current set of coding standards and best practices.

# CakePHP Conventions

When working in anything based on CakePHP like the [GrowServer](growserver.md) application
be sure to follow all [standard CakePHP conventions](https://book.cakephp.org/3.0/en/intro/conventions.html)
regarding casing and spacing. This will help the application simply work without needing
to configure things like table names etc.

# RegEx

Regular Expressions should be avoided unless absolutely necessary. They are difficult to
grok and maintain, and are almost always more fragile than expected, leading to unexpected
and difficult to diagnose problems.

# Feature Flags

Any new feature should be put behind a [Feature Flag](../FeatureFlags/overview.md). This allows
us to not only roll out new features incrementally to different clients after a release,
as well as specifically gate certain features based on different subscription plan levels,
but also allows us to effectively 'roll back' one specific feature in a release, without
needing to rollback the entire release.

# Thin Controllers, Thin Models, and Thin Service Layers

[This article](http://blog.joncairns.com/2013/04/fat-model-skinny-controller-is-a-load-of-rubbish/) raises
some fantastic points regarding the old Thin Controllers, Thick Models argument.

[This repo](https://github.com/burzum/cakephp-service-layer) contains some general guidelines and
notes regarding the same.

Please read both of these thoroughly, and if any aspect is unclear discuss it with a teammate.

# Comments

More comments are better than less, always. Comments are not for you, they are for someone reading
your code who is not familiar with it. This includes your colleagues, QA, code reviewers,
and you, in sixth months.

Every function definition should have at least a 1 line comment above with a brief overview
of what it does, where it is called from, and why.

# Documentation

Documentation should be a first class citizen. Documentation should always be written before
implementation is begun. Whenever you are confused about how a feature should work, refer
to the documentation, if the documentation is incomplete, stop all work, and flesh out
the documentation more fully.
