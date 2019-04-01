<?php
namespace App\Test\TestCase\Model\Table;

use App\Model\Table\ExportsTable;
use Cake\ORM\TableRegistry;
use Cake\TestSuite\TestCase;

class ExportsTableTest extends TestCase
{
    public $Exports;

    public $fixtures = [
        'app.Exports',
        'app.QueuedJobs',
        'app.Users'
    ];

    public function setUp()
    {
        parent::setUp();

        \Cake\Core\Configure::write('Acl.database', 'test');
        \Cake\Core\Plugin::load('Queue');
        $this->Exports = TableRegistry::get('Exports');
    }

    public function tearDown()
    {
        unset($this->Exports);
        parent::tearDown();
    }

    public function testGetExportsOf()
    {
        $expected = [
            [
                'name' => 'Kazuki\'s sentences',
                'status' => 'online',
                'url' => 'https://downloads.tatoeba.org/exports/kazuki_sentences.zip',
            ],
            [
                'name' => 'Japanese-Russian sentence pairs',
                'status' => 'queued',
                'url' => null,
            ],
        ];

        $result = $this->Exports->getExportsOf(7);

        $this->assertEquals($expected, $result->hydrate(false)->toArray());
    }

    public function testCreateExport_returnsExport()
    {
        $expected = [
            'name' => 'foo',
            'url' => null,
            'status' => 'queued',
        ];
        $options = [ 'type' => 'list', 'name' => 'foo', 'description' => 'bar' ];

        $export = $this->Exports->createExport(4, $options);

        $this->assertEquals($expected, $export);
    }

    public function testCreateExport_createsExport()
    {
        $expected = [
            'id' => 4,
            'name' => 'foo',
            'description' => 'bar',
            'url' => null,
            'filename' => null,
            'generated' => null,
            'status' => 'queued',
            'queued_job_id' => 4,
            'user_id' => 4,
        ];
        $options = [ 'type' => 'list', 'name' => 'foo', 'description' => 'bar' ];

        $this->Exports->createExport(4, $options);

        $export = $this->Exports->find()->hydrate(false)->last();
        $this->assertEquals($expected, $export);
    }

    public function testCreateExport_createsJob()
    {
        $options = [ 'type' => 'list', 'name' => 'foo', 'description' => 'bar' ];

        $this->Exports->createExport(4, $options);

        $job = $this->Exports->QueuedJobs->find()->last();
        $this->assertEquals('Export', $job->job_type);
        $this->assertEquals(4, $job->job_group);

        $export = $this->Exports->find()->last();
        $this->assertEquals($export->id, unserialize($job->data)['export_id']);
    }
}
