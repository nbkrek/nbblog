.. vim: set tw=80 :

#######################
Development Environment
#######################

Assumptions
===========
You are running Ubuntu 14.04 on your system.

Howto setup
===========
Install vagrant and virtualbox::

    sudo apt-get install vagrant virtualbox

Upgrade vagrant to the latest version::

    wget https://releases.hashicorp.com/vagrant/1.8.4/vagrant_1.8.4_x86_64.deb
    sudo dpkg -i vagrant_1.8.4_x86_64.deb


Howto start the dev environment
===============================
Switch to 'this' folder - i.e. the folder this readme is in and run::

    vagrant up

Then the devlopment system can be reached using 'http://192.168.33.10'

