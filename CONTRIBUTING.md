Contributing
============

Coding standards
----------------

You must respect [Symfony coding standards]. This implies [PSR-0],
[PSR-1], and [PSR-2].

[Symfony coding standards]: http://symfony.com/doc/current/contributing/code/standards.html
[PSR-0]: http://www.php-fig.org/psr/psr-0/
[PSR-1]: http://www.php-fig.org/psr/psr-1/
[PSR-2]: http://www.php-fig.org/psr/psr-2/

Be sure to respect [Symfony naming conventions] too (part of Symfony
coding standards).

[Symfony naming conventions]: http://symfony.com/doc/current/contributing/code/conventions.html

You are strongly encouraged to use the [PHP Coding Standards Fixer] tool
to fix your code for you. It's installed with Composer development
dependencies for this project, and you can execute it by running `make
cs-fix`.

[PHP Coding Standards Fixer]: http://cs.sensiolabs.org/

When writing documentation (including comments), and issues, it's
appreciated to respect these [English typography guidelines].

[English typography guidelines]: https://github.com/valeriangalliat/typography

Language
--------

Code, documentation, issues (in fact, everything) should be written in
English.

*   Most programming languages' syntax is in English (and for sure PHP
    syntax is), and it would be rather inconsistent to not follow this
    choice for our own code.

*   By writing in English, we open our code and documentation to a wider
    range of people in the world.From the [Python Style Guide]:

    > Python coders from non-English speaking countries: please write
    > your comments in English, unless you are 120% sure that the code
    > will never be read by people who don't speak your language.

*   Writing English will certainly not be a choice for you if you decide
    to contribute to [FOSS software] or if you work in a multinational
    company.

*   I don't like having to put a [non-breaking space] before every
    colon, semicolon and question/exclamation marks in French.

[Python Style Guide]: https://www.python.org/dev/peps/pep-0008/
[FOSS software]: https://en.wikipedia.org/wiki/Free_and_open-source_software
[non-breaking space]: https://en.wikipedia.org/wiki/Non-breaking_space

For issues (if we end up using them), it's acceptable to communicate in
French since they should be used practically only internally, however I
believe it would be a great exercise for everyone to stick to English
there.

Developing a feature
--------------------

We're trying to stay close to the well-known [successful Git branching
model][sgbm].

*   Create a branch for each new feature, or bug fix (or quite
    everything in fact).
*   Base feature branch from the `develop` branch.
*   Once a feature is done, open a [pull request] on the GitHub
    repository, let the other team members review the code, apply fixes
    if needed, and merge in `develop` once everything is fine. *Leaving
    a reasonable time frame for code review is important!*
*   When a release is ready  merge `develop` in `master`, and add a
    version tag if we decide to do [Versioning](#versioning).

[sgbm]: http://nvie.com/posts/a-successful-git-branching-model/
[pull request]: https://help.github.com/articles/using-pull-requests/

Versioning
----------

I'm not sure this will be needed for this project, but if we end up
versioning it, let's follow [Semantic Versioning] rules.

[Semantic Versioning]: http://semver.org/

Every release should be tagged with the new version number. That is, if
you just released 1.4.0, run `git tag -a 1.4.0`.

Also, every bug fix on `master` should be tagged with a new version
(after bumping the patch number).
