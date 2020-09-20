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
    );
```

```
    /// add r ranks relation that gets all ranks that start with r except the user's current rank 
    $users = $users->dynamicLoad(
        'r_ranks', 
        fn($m) => Rank::where('name', 'like', '%r')->where('id', '!=', $m->rank_id)
        );
```
