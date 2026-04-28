<?php
require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';

use App\Models\User;
use Illuminate\Http\UploadedFile;

// Use real file
$realFile = new UploadedFile('/tmp/test_avatar.jpg', 'test_avatar.jpg', 'image/jpeg', null, true);
$user = User::find(16);

echo "User: " . $user->nom . "\n";
echo "Current avatar: " . ($user->avatar ?? 'NULL') . "\n";

// Test storing the file
$path = $realFile->store('avatars', 'public');
echo "Stored path: " . $path . "\n";

// Test updating
$result = $user->updateProfile(['avatar' => $path]);
echo "Update result: " . ($result ? 'true' : 'false') . "\n";

// Check database
$fresh = $user->fresh();
echo "Avatar after update: " . ($fresh->avatar ?? 'NULL') . "\n";

// Check file exists
$filePath = 'storage/app/public/' . $path;
echo "File exists: " . (file_exists($filePath) ? 'yes' : 'no') . "\n";
echo "File size: " . (file_exists($filePath) ? filesize($filePath) : 'N/A') . " bytes\n";
