<?php

declare(strict_types=1);

use PHPUnit\Framework\TestCase;
use Models\UserFileModel;

class UserFileModelTest extends TestCase
{
    private string $testFilePath;
    private UserFileModel $userFileModel;
    private array $testUserData = [
        'id' => '1',
        'email' => 'test@example.com',
        'nickname' => 'testUser',
        'birthdate' => '2000-01-01',
        'password_hash' => 'hashedpassword'
    ];

    protected function setUp(): void
    {
        $this->testFilePath = __DIR__ . '/test_data/users.json';

        if (file_exists($this->testFilePath)) {
            unlink($this->testFilePath);
        }

        $directory = dirname($this->testFilePath);
        if (is_dir($directory)) {
            rmdir($directory);
        }

        $this->userFileModel = new UserFileModel($this->testFilePath);
    }

    protected function tearDown(): void
    {
        if (file_exists($this->testFilePath)) {
            unlink($this->testFilePath);
        }

        $directory = dirname($this->testFilePath);
        if (is_dir($directory)) {
            rmdir($directory);
        }
    }

    // ------------------------------ //
    //      Test the constructor      //
    // ------------------------------ //

    // Test the case where the file and directory are created by the constructor
    public function testConstructorCreatesFileAndDirectory()
    {
        $this->assertFileExists($this->testFilePath);
        $this->assertTrue(is_dir(dirname($this->testFilePath)));
    }

    // ------------------------------------ //
    //      Test the createUser method      //
    // ------------------------------------ //

    // Test the case where the user is successfully created and stored in the file
    public function testCreateUserSuccess()
    {
        // Call the method
        $this->userFileModel->createUser($this->testUserData);

        // Assert that the user was stored
        $users = json_decode(file_get_contents($this->testFilePath), true);
        $this->assertIsArray($users);
        $this->assertCount(1, $users);
        $this->assertEquals($this->testUserData, $users[0]);
    }

    // Test the case where the user is not created because the email already exists
    public function testCreateUserDuplicateEmail()
    {
        // Call the method
        $this->userFileModel->createUser($this->testUserData);
        $result2 = $this->userFileModel->createUser($this->testUserData);

        // Assert that the user was stored only once, and the second call returned null
        $users = json_decode(file_get_contents($this->testFilePath), true);
        $this->assertIsArray($users);
        $this->assertCount(1, $users);
        $this->assertEquals($this->testUserData, $users[0]);
        $this->assertNull($result2);
    }

    // Test the case where multiple users are successfully created and stored in the file
    public function testCreateUserMultipleSuccess()
    {
        $data2 = [
            'id' => '2',
            'email' => 'test2@example.com',
            'nickname' => 'newUser2',
            'birthdate' => '2000-01-01',
            'password_hash' => 'hashedpassword'
        ];

        // Call the method
        $this->userFileModel->createUser($this->testUserData);
        $this->userFileModel->createUser($data2);

        // Assert that the users were stored
        $users = json_decode(file_get_contents($this->testFilePath), true);
        $this->assertIsArray($users);
        $this->assertCount(2, $users);
        $this->assertEquals($this->testUserData, $users[0]);
        $this->assertEquals($data2, $users[1]);
    }

    // --------------------------------------- //
    //      Test the getUserByType method      //
    // --------------------------------------- //

    // Test the case where the user is successfully retrieved by email
    public function testGetUserByTypeWithTypeEmailSuccess()
    {
        // Add a user to the file
        $this->userFileModel->createUser($this->testUserData);

        $result = $this->userFileModel->getUserByType('email', $this->testUserData['email']);

        // Assert that the result is as expected    
        $this->assertEquals($this->testUserData, $result);
    }

    // Test the case where the user is not retrieved by email because it does not exist
    public function testGetUserByTypeWithTypeEmailNotExisting()
    {
        $result = $this->userFileModel->getUserByType('email', 'test@example.com');

        // Assert that the result is as expected    
        $this->assertNull($result);
    }

    // Test the case where the user is successfully retrieved by id
    public function testGetUserByTypeWithTypeIdSuccess()
    {
        // Add a user to the file
        $this->userFileModel->createUser($this->testUserData);

        $result = $this->userFileModel->getUserByType('id', $this->testUserData['id']);

        // Assert that the result is as expected    
        $this->assertEquals($this->testUserData, $result);
    }

    // Test the case where the user is not retrieved by id because it does not exist
    public function testGetUserByTypeWithTypeIdNotExisting()
    {
        $result = $this->userFileModel->getUserByType('id', '1');

        // Assert that the result is as expected    
        $this->assertNull($result);
    }

    // ------------------------------------ //
    //      Test the updateUser method      //
    // ------------------------------------ //

    // Test the case where the user is successfully updated
    public function testUpdateUserSuccess()
    {
        // Add a user to the file
        $this->userFileModel->createUser($this->testUserData);

        $data = [
            'id' => $this->testUserData['id'],
            'email' => $this->testUserData['email'],
            'nickname' => 'updatedUser',
            'birthdate' => '2004-01-08',
            'password_hash' => 'hashedpassword'
        ];

        $result = $this->userFileModel->updateUser($data);

        // Assert that the result is as expected    
        $this->assertEquals($data, $result);
    }

    // Test the case where the user is not updated because it does not exist
    public function testUpdateUserNotExisting()
    {
        $result = $this->userFileModel->updateUser($this->testUserData);

        // Assert that the result is as expected    
        $this->assertNull($result);
    }
}
