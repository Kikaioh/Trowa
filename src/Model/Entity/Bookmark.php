<?php
namespace App\Model\Entity;

use Cake\ORM\Entity;
use Cake\Collection\Collection;

/**
 * Bookmark Entity
 *
 * @property int $id
 * @property int $user_id
 * @property string $title
 * @property string $description
 * @property string $url
 * @property \Cake\I18n\Time $created
 * @property \Cake\I18n\Time $modified
 *
 * @property \App\Model\Entity\User $user
 * @property \App\Model\Entity\Tag[] $tags
 */
class Bookmark extends Entity
{

    /**
     * Fields that can be mass assigned using newEntity() or patchEntity().
     *
     * Note that when '*' is set to true, this allows all unspecified fields to
     * be mass assigned. For security purposes, it is advised to set '*' to false
     * (or remove it), and explicitly make individual fields accessible as needed.
     *
     * @var array
     */
    protected $_accessible = [
        '*' => true,
        'id' => false,
		'tag_string' => true
    ];


	// Whenever you call for a tagString from the entity object, you'll get this
	protected function _getTagString()
	{
		if(isset($this->_properties['tag_string'])) {
			return $this->_properties['tag_string'];
		}
		if(empty($this->tags)) {
			return '';
		}
		$tags = new Collection($this->tags);
		$str = $tags->reduce(
			function($string, $tag) {
				return $string . $tag->title . ', ';
			},
			''
		);
		return trim($str, ', ');
	}

	public static function test()
    {
        echo "whoa!";
    }

	
}
