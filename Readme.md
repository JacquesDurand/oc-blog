[![Security Rating](https://sonarcloud.io/api/project_badges/measure?project=JacquesDurand_oc-blog&metric=security_rating)](https://sonarcloud.io/dashboard?id=JacquesDurand_oc-blog) [![Maintainability Rating](https://sonarcloud.io/api/project_badges/measure?project=JacquesDurand_oc-blog&metric=sqale_rating)](https://sonarcloud.io/dashboard?id=JacquesDurand_oc-blog) [![Vulnerabilities](https://sonarcloud.io/api/project_badges/measure?project=JacquesDurand_oc-blog&metric=vulnerabilities)](https://sonarcloud.io/dashboard?id=JacquesDurand_oc-blog) [![Bugs](https://sonarcloud.io/api/project_badges/measure?project=JacquesDurand_oc-blog&metric=bugs)](https://sonarcloud.io/dashboard?id=JacquesDurand_oc-blog)  
[![SonarCloud](https://sonarcloud.io/images/project_badges/sonarcloud-white.svg)](https://sonarcloud.io/dashboard?id=JacquesDurand_oc-blog)
# Parcours OpenClassrooms: Développeur d'application PHP/Symfony

## Projet 5: Créer un blog en PHP

-----------------------------------------------

## Description

The goal of this project was to create a blog from scratch in a recent version of PHP without the use of any framework (
nor front neither back).  
It had to contain:

- A minimum of the following entities:
  - Some **Users**
  - Some blog **Posts**
  - The possibility to leave a **Comment** on a blog post
- A front office with:
  - An index
  - A navigation menu
  - Pages for a single/multiple blog posts
  - Forms to:
    - leave a comment on a post
    - contact the Admin (myself)
    - register/login as a **User**
- A back office with:
  - everything needed to manage the *CRUD* (Creation, Update, Read, Deletion) of each entity

**NOTA BENE :**  
The back end has been realised in **PHP 7.4**.  
The front end has been realised through the use of [**Tailwind CSS**](https://tailwindcss.com/) and a tiny bit of
Vanilla **Javascript**

## Table of contents

- [Installation](#Installation)
  - [Prerequisites](#Prerequisites)
    - [Git](#Git)
    - [Docker](#Docker)
    - [Docker-compose](#Docker-Compose)
  - [Clone](#clone)

- [Configuration](#configuration)
- [Getting started](#getting-started)

## Installation

### Prerequisites

#### Git

To be able to locally use this project, you will need to install [Git](https://git-scm.com/) on your machine.  
Follow the installation instructions [here](https://git-scm.com/downloads) depending on your Operating system.

#### Docker

This project runs 3 separate applications each in their own containers:

1. The PostgreSql DataBase
2. The Nginx Server
3. The PHP application (blog) itself

Each is based upon its own Docker Image.  
To have them run locally, you will need to install [Docker](https://www.docker.com/) on your machine.  
Follow the installation instructions [here](https://docs.docker.com/get-docker/) for most OS
or [here](https://wiki.archlinux.org/title/Docker) for Archlinux.

#### Docker Compose

As defined on the documentation:
> Compose is a tool for defining and running multi-container Docker applications.

Since it is our case in this project, we also chose to use compose to build the complete project.  
You can see how to install it [here](https://docs.docker.com/compose/install/)

### Clone

Move to the parent directory where you wish to clone the project.

```shell
git clone https://github.com/JacquesDurand/oc-blog.git
```

Then move into the newly cloned directory

```shell
cd oc-blog
```

## Configuration

This project relies on the use of environment variables, which act as *secrets*. These are for instance the database
connection information.  
To prevent sharing my personal information, I didn't commit the **.env** file where they are contained.  
I did commit a **.env.dist** where you can find the variables needed to launch this project.  
Once inside **oc-blog** directory:

```shell
cp .env.dist .env
```

Then open your newly created **.env** with [your favorite text editor](https://neovim.io/) and replace the different *"
CHANGEME"* values by your own.  
You might want to keep

```dotenv
DB_PORT= 5432
```

since the postgres image will run on this port.  
Here is an example of **.env** for this project:

```dotenv
DB_HOST= 'db'
DB_PORT= 5432
DB_NAME= 'mydb'
DB_USERNAME= 'myUser'
DB_PASSWORD= 'myPassword'
```

## Getting Started

Now that everything has been configured, let us get into it !  
Still at the root of **oc-blog**, run :

```shell
docker-compose up -d
```

If everything went fine, you should be able to navigate to [localhost](http://localhost:80) and start looking around my
blog.  
If not, please do not hesitate to [submit an issue](https://github.com/JacquesDurand/oc-blog/issues/new) and I'll get
back to you *ASAP*.