# DynamicLoading

### Basic usage
You do not need a foreign key connecting your tables.



```
$collectionOfModels->dynamicLoad(
        'name_of_custom_relation', // this is only defined here and where the data is accessed
        function ($model) {
            
            return RelatedModel::where('conditions', $model->conditions)->orderBy('condition');
     
        },

        'relation_key', // Optional if this does not exist it will be created as an alias

        'model_key' // Optional used to match the records back to the model
    );
```
If relation_key and model_key are not defined they will be created 
`model_key: {primary_key} relation_key: {model_name}_{primary_key}`

## Examples

#### Ranks user doesn't have ending with 'r'
Given the following database structure it's easy to add a relation for the current rank but not for ranks not associated with the user.
#### users
* id
* rank_id

#### ranks
* id
* name

```
    /// add r ranks relation that gets all ranks that start with r except the user's current rank 
    $users = $users->dynamicLoad(

        'r_ranks', 

        fn($m) => Rank::where('name', 'like', '%r')->where('id', '!=', $m->rank_id)
    );
```
This would produce 1 query and add the matching ranks query as a relationship to the the collection of users.
#### result
```
$users[0] => [
        'id' => 1,
        'rank_id' => 5,
        'r_ranks' => [
                0 => [
                    'id' => 6,
                    'name' => 'Performer'
                
                ],
                1 =>  => [
                    'id' => 8,
                    'name' => 'Racer'
                
                ]
        ]
]
```
This works by injecting the user_id in a subselect for each model and combining the subqueries with a union.
#### query produced
```
select * from (select *, 1 as user_id from "ranks" where "name" like '%r' and "id" != 1) 
        union select * from (select *, 2 as user_id from "ranks" where "name" like '%r' and "id" != 1) 
        union select * from (select *, 3 as user_id from "ranks" where "name" like '%r' and "id" != 1) 
        union select * from (select *, 4 as user_id from "ranks" where "name" like '%r' and "id" != 1)
```


Here we have a matching user_id on logins table but if we only want the latest login we have to return them all with a typical relationship. 
Instead we can dynamically load the just the latest login for a collection of users using just 1 query.
#### users
* id

#### logins
* id
* created_at
* user_id

```
   // get the latest login for user
    $users = $users->dynamicLoad(

        'latest_login', 

        fn($m) => Login::where('id', '!=', $m->id)->latest()

    );

```
The results would look something like this
```
$users => [
        'id' => 1,
        'latest_login' => [
                0 => [
                    'id' => 6,
                    'created_at' => '2020-01-20 10:10:00'
                
                ],
        ]
],
[
        'id' => 2,
        'latest_login' => [
                0 => [
                    'id' => 78,
                    'created_at' => '2020-01-25 15:10:00'
                
                ],
        ]
],
```

