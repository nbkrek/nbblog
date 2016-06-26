.. vim: set tw=80 :

######
nbblog
######

nbkr e.K.'s throw on setting up a blog system.


Development Environment
=======================
see vagrant/README.rst for details.


Conventions and Tools
=====================
We use the following tools, so it really helps if you are familar with them.

* git-flow
* vagrant
* composer
* phpunit (4.8)
* slim V3

Formating the articles
----------------------
The articles are formated using HTML without inline CSS. This way we don't have
to transform the contain again. The users of the blog know HTML so simplifing
the editing process by using RST, Markdown or any other formating setup is not
necessary.

By avoiding inline CSS we are flexible in the possiblities the blog has without
getting locked in the formating.

So in the end this is valid::

    <ul class="dotted">
        <li>jfdklafadjsl</li>
    </ul>

    <img class="left" ...>

while this is not ::

    <ul sytle="background-color:yellow">
        <li>jfdklafadjsl</li>
    </ul>

    <img style="border solid 1px" ...>

The template will provide the appropriate CSS classes.

Images
^^^^^^
Images are stored with the article files. So they are basically inside the
tar.bz2 files (see next section for details). 

This brings one problem. The path to the image would be::

    stub/images/bla.gif

Inside the text.xml it would be more convenient to use just 'images/bla.gif'.
This way changing the stub wouldn't require editing the text.xml.

If we do this that way we have to rewrite the index-Handler to add the stub to
image paths. That in turn requires a regex, so images should always follow this
template

    <img.*src="<yourimage>"

otherwise the regex won't work.
      
Packaging of articles
---------------------
Articles and every relateded files - images, videos - are stored as tar.bz2
archive. For publishing copy it to the articles folder of the system. The
filename of the tar.bz2 file will be used as the stub where the article is
published under. This way we have a way to make sure a stub isn't duplicated and
it can easily be found by the users and the system.
