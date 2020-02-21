# Working with our Documentation

For inspiration, start here: [GitLab's Handbook](https://about.gitlab.com/handbook/) or [Clef's Handbook](https://github.com/clef/handbook)

## Using these docs

This is our company documentation. Everything anyone needs to know about how to work at Grownetics should be here. Anything you would need to train someone else to do should have a step by step guide somewhere in this repository that you can point people to. Check out http://remoteonly.org/

Make changes, additions, and deletions where necessary. These docs become powerful only if everyone uses them and contributes.

We are basing our Handbook heavily off of GitLab's and their [page on how they use their handbook](https://about.gitlab.com/handbook/handbook-usage/) is a great resource.

## Organization

The Handbook/ folder should contain everything about how Grownetics works as a company. Project folders like Server/ or DevOps/ should contain everything about how those projects work.

Every folder should have a README.md that is the starting point for all documentation within that folder. Any additional documentation files in a project folder should be accessible through clickable links from the README.md or a page linked to from the README.md. We don't want any documentation files hanging around anywhere that cannot be accessed by clicking through correct links from the README.md in the root of the repository.

## Making Edits

### Style Guide

Every page should start with a level 1 heading with the name of that page, `# Page Name`.

That must be the only level 1 heading on any page. Further headings must be level 2 `## Heading` or lower `### Level 3`. Otherwise the Table of Contents on the right will not render correctly.

## Offline Access

Using VSCode with `Markdown Preview Enhanced` extension installed is the best interface for offline reading or editing of the documentation that we have found so far.

### mkdocs

We use mkdocs to render our handbook into a nice HTML page which is served at https://handbook.grownetics.co/

#### Local Editing

To see a local version that updates whenever you change a file, simply install mkdocs with the material theme `pip install mkdocs mkdocs-material` then run `mkdocs serve` from the root of the directory. This will spin up a server at http://127.0.0.1:8000

#### Docker Version

From the root directory simply run `docker run --rm -it -p 8000:8000 -v ${PWD}:/docs squidfunk/mkdocs-material`. This will spin up a server at http://127.0.0.1:8000

## Markdown

The docs use markdown as super simple way to do formatting, check out gitlab flavored markdown [here](https://github.com/gitlabhq/gitlabhq/blob/master/doc/user/markdown.md)

## Tips

### Line Breaks

Make sure to put two spaces at the end of a line to create a line break. If you put none and just hit enter it will render as one line.

### Don't duplicate

Make sure to use the search box up top first to find out if what you're about to add already exists somewhere else.

### Creating / Linking New Pages

Only make a new page if you have a bunch of content to put on that new page, having a list of links to empty pages isn't useful, and will waste people's time when they click links to nowhere. Instead, create a new header section (like the section you are currently reading). Once a sub-section gets too large, THEN create a new page for it. Don't create a page for content to go in later, create content first, and more pages as needed.

#### Adding New Page to Table of Contents

Assuming the above considerations for creating a new page were taken and before merging changes to the main branch, a new section in the table of contents will need to be created. In order to do this you must first manually link the page created to the mkdocs.yml file in the root directory of the Grownetics repo.  In order to do this open Grownetics/mkdocs.yml and manually add the page using the syntax below under the appropriate subheading in alphabetical order. 

`- 'PageName': 'subdirectory/page.md'` 

## Reviewing Handbook changes via GitLab

On any Merge Request, you can view what the Handbook will look like based on the current code in that branch by mousing over the far right circle next to the Pipeline section on the page, and clicking the start button next to 'Review Handbook'.

Once GitLab finishes building and spinning up the new Handbook, it will refresh with a URL you can click.
