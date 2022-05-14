# FSDB

The ultimate prototyping database on top of the filesystem.

## Introduction
I was always thinking what is the best way to implement a backend, especially a RESTful one.

Obviously, PHP is the best choice to me, just because of its Associative Array and interaction with json_encode/json_decode, but nothing else.

Usually, I was doing in this way:

```php
//TODO: put code here

```

However, this is not the best way to do it, becasuse serialization + unserialization for each request is very slow.

And also, data is unable to be accessed by multiple requests.

So, you may use frameworks like swoole or workerman, so to maintain the state of the system in an array, and then serialize it to json and unserialize it when needed. The array can then be protected by a mutex.

However, does it really solve the problem? No, because serialization + unserialization is still slow; and also, data is still unable to be accessed by multiple requests.

To make our life easier, I would like to have a database that can be accessed like this:

```php
$db['1'] = '1';
$db['2'] = ['testing' => '3'];
$db['2'] = [3, 2, 1, 4];
$db['3'] = ['asdf'];
echo json_encode($db);

foreach ($db as $k => $v) {
    echo `$k\n`;
    var_dump($v);
}

echo $db['2'][3];
```

FSDB implemented `\ArrayAccess`, `\JsonSerializable`, `\Iterator` and store the great assoc array in files in synchronous manner.

Hackathons can never be easier.

## Usage

[Example](example.php)

## Precautions

- `current()` of `\Iterator` will lead to iteration of a directory
- There may induce a lot of files in the database directory (may use an VFS to solve this problem?)