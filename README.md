# DynamicLoading

## Examples

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

```
    /// add r ranks relation that gets all ranks that start with r except the user's current rank 
    $users = $users->dynamicLoad(

        'r_ranks', 

        fn($m) => Rank::where('name', 'like', '%r')->where('id', '!=', $m->rank_id)
    );

   // get the latest login for user
    $users = $users->dynamicLoad(

        'latest_login', 

        fn($m) => Login::where('id', '!=', $m->id)->latest()

    );

```


