<?php

use Illuminate\Database\Capsule\Manager as DB;
use Michaeljoyner\Edible\Models\Page;
use Michaeljoyner\Edible\Models\Textblock;
use Michaeljoyner\Edible\Models\Gallery;
use Illuminate\Container\Container;

abstract class TestCase extends PHPUnit_Framework_TestCase {

  public function setUp()
    {
        $this->setUpDatabase();
        $this->migrateTables();
    }

    protected function setUpDatabase()
    {
        $database = new DB;

        $database->addConnection(['driver' => 'sqlite', 'database' => ':memory:']);
        $database->bootEloquent();
        $database->setAsGlobal();
    }

    protected function migrateTables()
    {
        DB::schema()->create('ec_pages', function($table) {
          $table->increments('id');
            $table->string('name')->unique();
            $table->text('description')->nullable();
            $table->timestamps();
        });

        DB::schema()->create('ec_textblocks', function($table) {
          $table->increments('id');
            $table->integer('ec_page_id')->unsigned();
            $table->foreign('ec_page_id')->references('id')->on('ec_pages')->onDelete('cascade');
            $table->string('name');
            $table->text('description');
            $table->text('content')->nullable();
            $table->boolean('allows_html');
            $table->timestamps();
        });

        DB::schema()->create('ec_galleries', function($table) {
          $table->increments('id');
            $table->integer('ec_page_id')->unsigned();
            $table->foreign('ec_page_id')->references('id')->on('ec_pages')->onDelete('cascade');
            $table->string('name');
            $table->text('description');
            $table->integer('is_single')->default(0);
            $table->timestamps();
        });

        DB::schema()->create('media', function($table) {
          $table->increments('id');
          $table->morphs('model');
          $table->string('collection_name');
          $table->string('name');
          $table->string('file_name');
          $table->string('disk');
          $table->unsignedInteger('size');
          $table->text('manipulations');
          $table->text('custom_properties');
          $table->unsignedInteger('order_column')->nullable();
          $table->timestamps();
        });
    }

    protected function seeInDatabase($table, $attributes) {
      switch ($table) {
        case 'ec_pages':
          $q = null;
          foreach ($attributes as $key => $value) {
            if(is_null($q)) {
              $q = Page::where($key, $value);
            } else {
              $q = $q->where($key, $value);
            }
          }
          $this->assertCount(1, $q->get());
          break;

        case 'ec_textblocks':
          $q = null;
          foreach ($attributes as $key => $value) {
            if(is_null($q)) {
              $q = Textblock::where($key, $value);
            } else {
              $q = $q->where($key, $value);
            }
          }
          $this->assertCount(1, $q->get());
          break;
        case 'ec_galleries':
          $q = null;
          foreach ($attributes as $key => $value) {
            if(is_null($q)) {
              $q = Gallery::where($key, $value);
            } else {
              $q = $q->where($key, $value);
            }
          }
          $this->assertCount(1, $q->get());
          break;
        default:
          # code...
          break;
      }
    }

    protected function notSeeInDatabase($table, $attributes) {
      switch ($table) {
        case 'ec_pages':
          $q = null;
          foreach ($attributes as $key => $value) {
            if(is_null($q)) {
              $q = Page::where($key, $value);
            } else {
              $q = $q->where($key, $value);
            }
          }
          $this->assertCount(0, $q->get());
          break;

        case 'ec_textblocks':
          $q = null;
          foreach ($attributes as $key => $value) {
            if(is_null($q)) {
              $q = Textblock::where($key, $value);
            } else {
              $q = $q->where($key, $value);
            }
          }
          $this->assertCount(0, $q->get());
          break;
        case 'ec_galleries':
          $q = null;
          foreach ($attributes as $key => $value) {
            if(is_null($q)) {
              $q = Gallery::where($key, $value);
            } else {
              $q = $q->where($key, $value);
            }
          }
          $this->assertCount(0, $q->get());
          break;
        default:
          # code...
          break;
      }
    }
}
