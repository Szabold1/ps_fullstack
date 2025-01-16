<?php

declare(strict_types=1);

use PHPUnit\Framework\TestCase;
use Framework\Validate;

class ValidateTest extends TestCase
{
    public function testEmail()
    {
        $this->assertTrue(Validate::email('test@example.com'));

        $this->assertFalse(Validate::email('test'));
        $this->assertFalse(Validate::email('test@'));
        $this->assertFalse(Validate::email('test@com'));
        $this->assertFalse(Validate::email('test@com.'));
        $this->assertFalse(Validate::email('test@example.com.'));
    }

    public function testNickname()
    {
        $this->assertTrue(Validate::nickname('test'));
        $this->assertTrue(Validate::nickname('test1'));
        $this->assertTrue(Validate::nickname('t1'));

        $this->assertFalse(Validate::nickname('t'));
        $this->assertFalse(Validate::nickname('1'));
        $this->assertFalse(Validate::nickname(''));
        $this->assertFalse(Validate::nickname('t!'));
        $this->assertFalse(Validate::nickname('abcdefghijklmnopqrstuvwxyz1234567890abcdefghijklmnopqrstuvwxyz1234567890'));
    }

    public function testBirthdate()
    {
        $this->assertTrue(Validate::birthdate('2000-01-01'));
        $this->assertTrue(Validate::birthdate('2010-11-22'));
        $this->assertTrue(Validate::birthdate('2015-01-01'));
        $this->assertTrue(Validate::birthdate('1930-01-01'));

        $this->assertFalse(Validate::birthdate('2000-01-32'));
        $this->assertFalse(Validate::birthdate('2000-13-01'));
        $this->assertFalse(Validate::birthdate('2020-01-01'));
        $this->assertFalse(Validate::birthdate('1910-01-01'));
    }

    public function testPassword()
    {
        $this->assertTrue(Validate::password('Password1'));
        $this->assertTrue(Validate::password('Password1!?@'));

        $this->assertFalse(Validate::password('Password'));
        $this->assertFalse(Validate::password('Pas1!'));
        $this->assertFalse(Validate::password('password1'));
        $this->assertFalse(Validate::password('PASSWORD1'));
        $this->assertFalse(Validate::password('PASSWORD1password1!?@PASSWORD1password1!?@'));
    }
}
