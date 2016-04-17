# Edible

Maps a content structure (consisting of pages with textblocks and galleries) to Eloquent models in your Laravel application. The user, usually in some sort of admin section, can then edit the contents of the textblocks and galleries, but have no control over the creation and deletion of them.

## Installation and Setup

Get the package: `composer require michaeljoyner/edible`

Publish the migrations and views: `php artisan vendor:publish --provider="Michaeljoyner\Edible\EdibleServiceProvider"`

You will have to go to your `resources/views/vendor` folder to adjust the views to work with your view structure.

Run the migrations: `php artisan migrate`

## Your edible.yaml file

You need to create a file named `edible.yaml` in the root directory of your application. The file must adhere to the structure shown in this example:

````yaml
pages:
  home:
      description: The homepage
      textblocks:
          intro:
              description: The homepage intro
              allows_html: false
          spiel:
              description: Company story
              allows_html: true
      galleries:
          slider:
              description: Homepage banner slide images
              is_single: false
  about:
        description: The about page
        textblocks:
            intro:
                description: The about page intro
                allows_html: false
            spiel:
                description: My story
                allows_html: true
        galleries:
            slider:
                description: About page banner slide images
                is_single: false
````


The edible.yaml file describes a collection of pages. Each page has a description and a collection of textblocks and galleries. Each textblock has a description and a boolean flag to determine if the text will be edited as HTML (i.e. with a WYSIWYG editor) or as plain text. Each gallery has a description and a boolean flag to determine if it is a single image or a collection of images. If is__single is true, each image uploaded will overwrite the previous ones.

## Mapping to models

Once your edible.yaml file is complete, you may run `php artisan edible:map`. You will be shown a summary of the changes that will be made, and asked to confirm before proceeding. If you choose to proceed, the contents of your edible file will be mapped to Eloquent models and stored in your database.

## Updating the edible.yaml file
 
 To add a new page, textblock or gallery, just add it into your edible.yaml file as normal. Next time you run `php artisan edible:map`, they will be added. Existing content will be untouched.
 
 To remove a page, textblock or gallery, simply delete it from the edible.yaml file. Obviously this will also delete the associated content.
 
 *DO NOT* modify the name of an existing page, textblock or gallery as *YOU WILL LOSE THE EXISTING CONTENT*. Rather ensure you get the names and descriptions correct before pushing your changes live. By editing the name of an existing page, textblock or gallery, when you next map, it will treat that as a new instance, and delete the "old" one along with its contents.

