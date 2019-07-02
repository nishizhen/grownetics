# Changelog entries

This guide contains instructions for when and how to generate a changelog entry
file.

## Overview

Each bullet point, or **entry**, in our Server/CHANGELOG.md file is generated from a single data file in the Server/changelogs/ folder. The file is expected to be a [YAML] file in the following format:

```
---
title: "Change[log]s"
type: added
```

## Creating Changelog Entries

Simply run `growctl changelog add` to add a changelog entry. It will prompt you for details.

## What warrants a changelog entry?

- Any user-facing change **should** have a changelog entry. Example: "GitLab now
  uses system fonts for all text."
- A fix for a regression introduced and then fixed in the same release (i.e.,
  fixing a bug introduced during a monthly release candidate) **should not**
  have a changelog entry.
- Any developer-facing change (e.g., refactoring, technical debt remediation,
  test suite changes) **should not** have a changelog entry. Example: "Reduce
  database records created during Cycle Analytics model spec."
- _Any_ contribution from a community member, no matter how small, **may** have
  a changelog entry regardless of these guidelines if the contributor wants one.
  Example: "Fixed a typo on the search results page. (Jane Smith)"
- Performance improvements **should** have a changelog entry.

## Writing good changelog entries

A good changelog entry should be descriptive and concise. It should explain the
change to a reader who has _zero context_ about the change. If you have trouble
making it both concise and descriptive, err on the side of descriptive.

- **Bad:** Go to a project order.
- **Good:** Show a user's starred projects at the top of the "Go to project"
  dropdown.

The first example provides no context of where the change was made, or why, or
how it benefits the user.

- **Bad:** Copy [some text] to clipboard.
- **Good:** Update the "Copy to clipboard" tooltip to indicate what's being
  copied.

Again, the first example is too vague and provides no context.

- **Bad:** Fixes and Improves CSS and HTML problems in mini pipeline graph and
  builds dropdown.
- **Good:** Fix tooltips and hover states in mini pipeline graph and builds
  dropdown.

The first example is too focused on implementation details. The user doesn't
care that we changed CSS and HTML, they care about the _end result_ of those
changes.

- **Bad:** Strip out `nil`s in the Array of Commit objects returned from
  `find_commits_by_message_with_elastic`
- **Good:** Fix 500 errors caused by elasticsearch results referencing
  garbage-collected commits

The first example focuses on _how_ we fixed something, not on _what_ it fixes.
The rewritten version clearly describes the _end benefit_ to the user (fewer 500
errors), and _when_ (searching commits with Elasticsearch).

Use your best judgement and try to put yourself in the mindset of someone
reading the compiled changelog. Does this entry add value? Does it offer context
about _where_ and _why_ the change was made?