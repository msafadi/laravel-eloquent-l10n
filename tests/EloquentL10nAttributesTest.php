<?php

namespace Safadi\Tests;

use Illuminate\Database\Capsule\Manager;
use Illuminate\Database\Eloquent\Model;
use PHPUnit\Framework\TestCase;
use Safadi\Eloquent\L10n\Concerns\HasTranslatableAttributes;
use Safadi\Eloquent\L10n\Contracts\Translatable as TranslatableContract;

class EloquentL10nAttributesTest extends TestCase
{
    public function testEloquesntL10nSaveAndRead()
    {
        $post = $this->createTestPost();

        $this->assertEquals('Post Title', TestPost::find(1)->title);
        
        $this->assertEquals('Post Title', TestPost::useLocale('en')->find(1)->title);
        $this->assertEquals('عنوان المنشور', TestPost::useLocale('ar')->find(1)->title);
    }

    public function testEloquesntL10nRead()
    {
        $post = $this->createTestPost();
        
        $this->assertEquals('Post Title', $post->title);
        $this->assertEquals('Post Title', $post->setLocale('en')->title);
        $this->assertEquals('عنوان المنشور', $post->setLocale('ar')->title);
    }

    public function testEloquesntL10nUpdate()
    {
        $post = $this->createTestPost();
        
        $post->title = 'Updated Post Title';
        $post->setLocale('ar')->title = 'عنوان المنشور محدث';
        $post->save();

        $this->assertEquals('Updated Post Title', $post->setLocale('en')->title);
        $this->assertEquals('عنوان المنشور محدث', $post->setLocale('ar')->title);
    }

    protected function createTestPost()
    {
        $post = TestPost::create([
            'id' => 1,
            'title' => [
                'en' => 'Post Title',
                'ar' => 'عنوان المنشور',
            ],
            'content' => [
                'en' => 'Post content',
                'ar' => 'محتوى المنشور',
            ],
        ]);
        return $post;
    }

    protected function setUp(): void
    {
        TestApplication::getInstance()->boot();

        TestPost::locale('en');

        $db = new Manager;

        $db->addConnection([
            'driver' => 'sqlite',
            'database' => ':memory:',
        ]);

        $db->bootEloquent();
        $db->setAsGlobal();

        $this->createSchema();
    }

    /**
     * Tear down the database schema.
     */
    protected function tearDown(): void
    {
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
            $table->string('title');
            $table->text('content');
        });
    }
}

class TestPost extends Model implements TranslatableContract
{
    use HasTranslatableAttributes;

    protected $table = 'posts';

    protected $fillable = ['id', 'title', 'content'];

    public $timestamps = false;

    protected function translatableAttributes()
    {
        return [
            'title', 'content'
        ];
    }

}
