<?php
namespace App\Model\Table;

use Cake\ORM\Query;
use Cake\ORM\RulesChecker;
use Cake\ORM\Table;
use Cake\Validation\Validator;

/**
 * Bookmarks Model
 *
 * @property \Cake\ORM\Association\BelongsTo $Users
 * @property \Cake\ORM\Association\BelongsToMany $Tags
 *
 * @method \App\Model\Entity\Bookmark get($primaryKey, $options = [])
 * @method \App\Model\Entity\Bookmark newEntity($data = null, array $options = [])
 * @method \App\Model\Entity\Bookmark[] newEntities(array $data, array $options = [])
 * @method \App\Model\Entity\Bookmark|bool save(\Cake\Datasource\EntityInterface $entity, $options = [])
 * @method \App\Model\Entity\Bookmark patchEntity(\Cake\Datasource\EntityInterface $entity, array $data, array $options = [])
 * @method \App\Model\Entity\Bookmark[] patchEntities($entities, array $data, array $options = [])
 * @method \App\Model\Entity\Bookmark findOrCreate($search, callable $callback = null, $options = [])
 *
 * @mixin \Cake\ORM\Behavior\TimestampBehavior
 */
class BookmarksTable extends Table
{

    /**
     * Initialize method
     *
     * @param array $config The configuration for the Table.
     * @return void
     */
    public function initialize(array $config)
    {
        parent::initialize($config);

        $this->setTable('bookmarks');
        $this->setDisplayField('title');
        $this->setPrimaryKey('id');

        $this->addBehavior('Timestamp');

        $this->belongsTo('Users', [
            'foreignKey' => 'user_id',
            'joinType' => 'INNER'
        ]);
        $this->belongsToMany('Tags', [
            'foreignKey' => 'bookmark_id',
            'targetForeignKey' => 'tag_id',
            'joinTable' => 'bookmarks_tags'
        ]);
    }

    /**
     * Default validation rules.
     *
     * @param \Cake\Validation\Validator $validator Validator instance.
     * @return \Cake\Validation\Validator
     */
    public function validationDefault(Validator $validator)
    {
        $validator
            ->integer('id')
            ->allowEmpty('id', 'create');

        $validator
            ->allowEmpty('title');

        $validator
            ->allowEmpty('description');

        $validator
            ->allowEmpty('url');

        return $validator;
    }

    /**
     * Returns a rules checker object that will be used for validating
     * application integrity.
     *
     * @param \Cake\ORM\RulesChecker $rules The rules object to be modified.
     * @return \Cake\ORM\RulesChecker
     */
    public function buildRules(RulesChecker $rules)
    {
        $rules->add($rules->existsIn(['user_id'], 'Users'));

        return $rules;
    }


	// Controllers are slim!  Application logic is in the models! ...As in here!
	// The $query argument is a query builder instance.
	// Custom finder methods like this will always get a Query Builder object!
	// Isn't that convenient?
	// The $options array will contain the 'tags' option we passed (remember, in the controller?)
	// to find ('tagged') in our controller action.
    public function findTagged(Query $query, array $options) {
		// Since we're in a Table object, $this->find() gives us SQL access to the bookmarks table in the DB!
		// Here, we're searching for all bookmarks and pulling back a few columns
		$bookmarks = $this->find()
			->select(['id', 'user_id', 'url', 'title', 'description'])
			->where(['user_id IS' => $options['user_id']]);

		// If there weren't any tags in the URL
		if (empty($options['tags'])) {
			// We're going to do a left join of the bookmarks results with the Tags table!
			// But where the title is null...?
			// I guess because the options would have been titles, and since we got none, they would be null for our search?
			$bookmarks
				->leftJoinWith('Tags')
				->where(['Tags.title IS' => null]);
		} 
		// Otherwise, if we DID have tags
		else {
			// Do an INNER join with the tags table, where the title is in the set of options we received!
			// So, apparently their where function can take a straight up array for the IN argument of the SQL
			// That's pretty convenient I guess.  Not so convenient having to learn all of these unique conventions though!
			$bookmarks
				->innerJoinWith('Tags')
				->where(['Tags.title IN ' => $options['tags']]);
		}

		return $bookmarks->group(['Bookmarks.id']);
	}

	public function beforeSave($event, $entity, $options)
	{
		if ($entity->tag_string) {
			$entity->tags = $this->_buildTags($entity->tag_string);
		}
	}

	protected function _buildTags($tagString)
	{
		//Trim tags
		$newTags = array_map('trim', explode(',', $tagString));
	
		// Remove all empty tags
		$newTags = array_filter($newTags);

		// Reduce duplicated tags
		$newTags = array_unique($newTags);

		$out = [];
		$query = $this->Tags->find()->where(['Tags.title IN' => $newTags]);

		// Remove existing tags from the list of new tags.
		foreach ($query->extract('title') as $existing) {
			$index = array_search($existing, $newtags);
			if ($index !== false) {
				unset($newTags[$index]);
			}
		}

		// Add existing tags
		foreach ($query as $tag) {
			$out[] = $tag;
		}

		// Add new tags.
		foreach ($newTags as $tag) {
			$out[] = $this->Tags->newEntity(['title' => $tag]);
		}

		return $out;
	}

}













