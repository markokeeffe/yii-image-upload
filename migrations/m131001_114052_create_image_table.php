<?php

class m131001_114052_create_image_table extends CDbMigration
{


	// Use safeUp/safeDown to do migration with transaction
	public function safeUp()
	{
    $this->createTable('images', array(
      'id' => 'pk',
      'imageable_type' => 'string',
      'imageable_id' => 'int unsigned',
      'ext' => 'CHAR(3) NOT NULL',
      'mime' => 'VARCHAR(16) NOT NULL',
      'focal_point' => 'VARCHAR(11)',
      'local_sizes' => 'VARCHAR(1023)',
      'remote_sizes' => 'VARCHAR(1023)',
      'alt' => 'VARCHAR(255)',
      'caption' => 'VARCHAR(1023)',
      'align' => 'VARCHAR(6)',
      'status' => 'TINYINT(1) UNSIGNED DEFAULT "0"',
      'created_at' => 'TIMESTAMP',
      'updated_at' => 'TIMESTAMP',
    ));

    $this->createIndex('images_i1', 'images', 'imageable_type');
    $this->createIndex('images_i2', 'images', 'imageable_id');
	}

	public function safeDown()
	{
    $this->dropTable('images');
	}

}