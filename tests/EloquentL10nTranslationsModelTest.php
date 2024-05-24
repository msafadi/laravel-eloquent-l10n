<?php

namespace Safadi\Tests;

use Illuminate\Database\Capsule\Manager;
use Illuminate\Database\Eloquent\Model;
use PHPUnit\Framework\TestCase;
use Safadi\Eloquent\L10n\Concerns\HasTranslationsModel;
use Safadi\Eloquent\L10n\Contracts\Translatable as TranslatableContract;

class EloquentL10nTranslationsModelTest extends TestCase
{
    public function testEloquesntL10nSaveAndRead()
    {
        $post = $this->createTestPost();
        
        $this->assertEquals('Post Title', TestPost::find(1)->title);
        
        $this->assertEquals('Post Title', TestPost::locale('en')->find(1)->title);
        $this->assertEquals('عنوان المنشور', TestPost::locale('ar')->find(1)->title);
    }

    public function testEloquesntL10nUseLocale()
    {
        $this->createTestPost();

        //$this->assertEquals('Post Title', TestPost::find(1)->title);
        
        $this->assertEquals('Post Title', TestPost::useLocale('en')->find(1)->title);
        $this->assertEquals('عنوان المنشور', TestPost::useLocale('ar')->find(1)->title);
    }

    public function testEloquesntL10nUpdate()
    {
        $post = $this->createTestPost();
        $post->translate([
            'title' => 'Post Title Updated',
            'content' => 'Post content',
        ], 'en');

        $this->assertEquals('Post Title Updated', TestPost::locale('en')->find(1)->title);
    }

    public function testEloquesntL10nDelete()
    {
        $post = $this->createTestPost();

        $this->assertEquals(2, TestPost::find(1)->translations()->count());

        $post->deleteTranslation('en');
        $this->assertEquals(1, TestPost::find(1)->translations()->count());

        $post->deleteTranslation('ar');
        
        $this->assertEquals(0, TestPost::find(1)->translations()->count());
    }

    public function testEloquesntL10nCreateWithTranslations()
    {

        $post = TestPost::withTranslations([
            'en' => [
                'title' => 'Post Title',
                'content' => 'Post content',
            ],
            'ar' => [
                'title' => 'عنوان المنشور',
                'content' => 'محتوى المنشور',
            ],
        ])->create(['id' => 1]);

        $this->assertEquals('Post Title', TestPost::useLocale('en')->find(1)->title);
        $this->assertEquals('عنوان المنشور', TestPost::useLocale('ar')->find(1)->title);
    }

    protected function createTestPost()
    {
        $post = TestPost::create(['id' => 1]);
        $post->translate([
            'title' => 'Post Title',
            'content' => 'Post content',
        ], 'en');
        $post->translate([
            'title' => 'عنوان المنشور',
            'content' => 'محتوى المنشور',
        ], 'ar');
        return $post;
    }

    protected function setUp(): void
    {
        TestApplication::getInstance()->boot();
        //Event::fake();

        $db = new Manager;

        $db->addConnection([
            'driver' => 'sqlite',
            'database' => ':memory:',
        ]);

        $db->bootEloquent();
        $db->setAsGlobal();

        $this->createSchema();

        TestPost::locale('en');
    }

    /**
     * Tear down the database schema.
     */
    protected function tearDown(): void
    {
        $this->schema()->drop('posts_l10n');
        $this->schema()->drop('posts');
    }

    /**
     * Get a database connection instance.
     *
     * @return \Illuminate\Database\ConnectionInterface
     */
    protected function connection()
    {
        return Model::getConnectionResolver()->connection();
    }

    /**
     * Get a schema builder instance.
     *
     * @return \Illuminate\Database\Schema\Builder
     */
    protected function schema()
    {
        return $this->connection()->getSchemaBuilder();
    }

    /**
     * Setup the database schema.
     *
     * @return void
     */
    public function createSchema()
    {
        $this->schema()->create('posts', function ($table) {
            $table->increments('id');
        });

        $this->schema()->create('posts_l10n', function ($table) {
            $table->unsignedInteger('post_id');
            $table->string('locale');
            $table->string('title');
            $table->string('content');
            $table->timestamps();
            $table->primary(['post_id', 'locale']);
        });
    }
}

class TestPost extends Model implements TranslatableContract
{
    use HasTranslationsModel;

    protected $table = 'posts';

    protected $fillable = ['id'];

    public $timestamps = false;
}
