<?php

use PHPUnit\Framework\TestCase;
use Framework\Response;
use Framework\Database;
use Framework\Helper;
use Models\UserFileModel;
use Models\UserModel;

class UserModelTest extends TestCase
{
    private $mockDatabase;
    private $mockUserFileModel;
    private $userModel;
    private $mockPdoStatement;
    private array $testUserData = [
        'id' => '1',
        'email' => 'test@example.com',
        'nickname' => 'testUser',
        'birthdate' => '2000-01-01',
        'password_hash' => 'hashedpassword'
    ];

    protected function setUp(): void
    {
        // Mock the Database and UserFileModel classes
        $this->mockDatabase = $this->createMock(Database::class);
        $this->mockUserFileModel = $this->createMock(UserFileModel::class);
        $this->userModel = new UserModel($this->mockDatabase, $this->mockUserFileModel);

        // Mock the PDOStatement class
        $this->mockPdoStatement = $this->createMock(\PDOStatement::class);
    }

    // Helper function to mock the PDOStatement class with a given result to return when fetch is called
    // private function mockPdoStatement($result)
    // {
    //     $mockStatement = $this->createMock(\PDOStatement::class);
    //     $mockStatement->method('fetch')->willReturn($result);
    //     return $mockStatement;
    // }

    // --------------------------------------- //
    //      Test the getUserByType method      //
    // --------------------------------------- //

    // Test the case where the user is successfully retrieved from the database
    public function testGetUserByTypeFromDatabaseSuccess()
    {
        // Mock the database response
        $this->mockDatabase
            ->expects($this->once())
            ->method('getUserByType')
            ->with('email', $this->testUserData['email'])
            ->willReturn($this->testUserData);

        $result = $this->userModel->getUserByType('email', $this->testUserData['email'], true);

        // Assert that the result is as expected
        $this->assertEquals($this->testUserData, $result);
    }

    // Test the case where the user is not found in the database
    public function testGetUserByTypeFromDatabaseNotFound()
    {
        // Mock the database response
        $this->mockDatabase
            ->expects($this->once())
            ->method('getUserByType')
            ->with('email', $this->testUserData['email'])
            ->willReturn(null);

        $result = $this->userModel->getUserByType('email', $this->testUserData['email'], true);

        // Assert that the result is as expected
        $this->assertNull($result);
    }

    // Test the case where the user is successfully retrieved from the file
    public function testGetUserByTypeFromFileSuccess()
    {
        // Mock the file response
        $this->mockUserFileModel
            ->expects($this->once())
            ->method('getUserByType')
            ->with('email', $this->testUserData['email'])
            ->willReturn($this->testUserData);

        $result = $this->userModel->getUserByType('email', $this->testUserData['email'], false);

        // Assert that the result is as expected
        $this->assertEquals($this->testUserData, $result);
    }

    // Test the case where the user is not found in the file
    public function testGetUserByTypeFromFileNotFound()
    {
        // Mock the file response
        $this->mockUserFileModel
            ->expects($this->once())
            ->method('getUserByType')
            ->with('email', $this->testUserData['email'])
            ->willReturn(null);

        $result = $this->userModel->getUserByType('email', $this->testUserData['email'], false);

        // Assert that the result is as expected
        $this->assertNull($result);
    }

    // ------------------------------------ //
    //      Test the createUser method      //
    // ------------------------------------ //

    // Test the case where the user is created successfully
    public function testCreateUserSuccess()
    {
        $testData = $this->testUserData;
        unset($testData['id']);

        // Mock the database responses
        $this->mockDatabase
            ->expects($this->once())
            ->method('getUserByType')
            ->with('email', $this->testUserData['email'])
            ->willReturn(null);

        $this->mockDatabase
            ->expects($this->once())
            ->method('createUser')
            ->with($testData)
            ->willReturn($this->testUserData);

        // Mock the file response
        $this->mockUserFileModel
            ->expects($this->once())
            ->method('createUser')
            ->with($this->testUserData)
            ->willReturn($this->testUserData);

        // Call the createUser method
        $result = $this->userModel->createUser($testData);

        // Assert that the user was created
        $this->assertEquals($this->testUserData, $result);
    }

    // Test the case where the user is not created because it already exists
    public function testCreateUserAlreadyExists()
    {
        $testData = $this->testUserData;
        unset($testData['id']);

        // Mock the database response
        $this->mockDatabase
            ->expects($this->once())
            ->method('getUserByType')
            ->with('email', $this->testUserData['email'])
            ->willReturn($this->testUserData);

        // Call the createUser method
        $result = $this->userModel->createUser($testData);

        // Assert that the user was created
        $this->assertNull($result);
    }

    // ----------------------------------- //
    //      Test the loginUser method      //
    // ----------------------------------- //

    // Test the case where the user is logged in successfully
    public function testLoginUserSuccess()
    {
        $hashedPassword = password_hash('Password1', PASSWORD_DEFAULT);
        $this->testUserData['password_hash'] = $hashedPassword;

        // Mock the database response with the hashed password
        $this->mockDatabase
            ->expects($this->once())
            ->method('getUserByType')
            ->with('email', $this->testUserData['email'])
            ->willReturn($this->testUserData);

        // Call the loginUser method with unhashed password
        $result = $this->userModel->loginUser([
            'email' => $this->testUserData['email'],
            'password' => 'Password1'
        ]);

        // Assert that the user was logged in
        $this->assertEquals($this->testUserData, $result);
    }

    // Test the case where login fails because the email is incorrect
    public function testLoginUserIncorrectEmail()
    {
        $hashedPassword = password_hash('Password1', PASSWORD_DEFAULT);
        $this->testUserData['password_hash'] = $hashedPassword;

        // Mock the database response with the hashed password
        $this->mockDatabase
            ->expects($this->once())
            ->method('getUserByType')
            ->with('email', 'invalid-email')
            ->willReturn(null);

        // Call the loginUser method
        $result = $this->userModel->loginUser([
            'email' => 'invalid-email',
            'password' => 'Password1'
        ]);

        // Assert that the user was not logged in
        $this->assertNull($result);
    }

    // Test the case where the login fails because the password is incorrect
    public function testLoginUserIncorrectPassword()
    {
        $hashedPassword = password_hash('Password1', PASSWORD_DEFAULT);
        $this->testUserData['password_hash'] = $hashedPassword;

        // Mock the database response with the hashed password
        $this->mockDatabase
            ->expects($this->once())
            ->method('getUserByType')
            ->with('email', $this->testUserData['email'])
            ->willReturn($this->testUserData);

        // Call the loginUser method
        $result = $this->userModel->loginUser([
            'email' => $this->testUserData['email'],
            'password' => 'InvalidPassword'
        ]);

        // Assert that the user was not logged in
        $this->assertNull($result);
    }

    // ------------------------------------ //
    //      Test the updateUser method      //
    // ------------------------------------ //

    // Test the case where the user is updated successfully when the password is not changed
    public function testUpdateUserPasswordNotChangedSuccess()
    {
        $toUpdateData = [
            'id' => $this->testUserData['id'],
            'nickname' => 'newNickname',
            'birthdate' => '2001-01-01',
            'password_hash' => null
        ];

        $expectedUser = array_merge($this->testUserData, $toUpdateData);

        // Mock the database response
        $this->mockDatabase
            ->expects($this->once())
            ->method('getUserByType')
            ->with('id', $this->testUserData['id'])
            ->willReturn($this->testUserData);

        $this->mockDatabase
            ->expects($this->once())
            ->method('updateUser')
            ->with($toUpdateData)
            ->willReturn($expectedUser);

        // Mock the file response
        $this->mockUserFileModel
            ->expects($this->once())
            ->method('updateUser')
            ->with($toUpdateData)
            ->willReturn($expectedUser);

        // Call the updateUser method
        $result = $this->userModel->updateUser($toUpdateData);

        // Assert that the user was updated
        $this->assertEquals($expectedUser, $result);
    }

    // Test the case where the user is updated successfully when the password is changed
    public function testUpdateUserPasswordChangedSuccess()
    {
        $toUpdateData = [
            'id' => $this->testUserData['id'],
            'nickname' => 'newNickname',
            'birthdate' => $this->testUserData['birthdate'],
            'password_hash' => 'newPasswordHash'
        ];

        $expectedUser = array_merge($this->testUserData, $toUpdateData);

        // Mock the database response
        $this->mockDatabase
            ->expects($this->once())
            ->method('getUserByType')
            ->with('id', $this->testUserData['id'])
            ->willReturn($this->testUserData);

        $this->mockDatabase
            ->expects($this->once())
            ->method('updateUser')
            ->with($toUpdateData)
            ->willReturn($expectedUser);

        // Mock the file response
        $this->mockUserFileModel
            ->expects($this->once())
            ->method('updateUser')
            ->with($toUpdateData)
            ->willReturn($expectedUser);

        // Call the updateUser method
        $result = $this->userModel->updateUser($toUpdateData);

        // Assert that the user was updated
        $this->assertEquals($expectedUser, $result);
    }

    // Test the case where the user is not updated because it does not exist
    public function testUpdateUserUserDoesNotExist()
    {
        // Mock the database response
        $this->mockDatabase
            ->expects($this->once())
            ->method('getUserByType')
            ->with('id', $this->testUserData['id'])
            ->willReturn(null);

        // Call the updateUser method
        $result = $this->userModel->updateUser($this->testUserData);

        // Assert that the user was not updated
        $this->assertNull($result);
    }
}
