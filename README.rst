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
      
Packaging of articles
---------------------
Articles and every relateded files - images, videos - are stored as tar.bz2
archive. For publishing copy it to the articles folder of the system. The
filename of the tar.bz2 file will be used as the stub where the article is
published under. This way we have a way to make sure a stub isn't duplicated and
it can easily be found by the users and the system.
