# Wordpress Custom Database Helper

This [class abstract](jay_custom_database_helper.php) helps to manage a custom database in WordPress, so you can easily bump the version number and add the required SQL for the update

There is also an [example class](example_custom_database.php) to help show how to use the abstract

###Default Values
Default values can be set by extending the class, or with a filter.
The Name of the filter is TableName_ColumnName_default e.g. If the table name is search_queries and the column is datetime the filter to use is search_queries_datetime_default