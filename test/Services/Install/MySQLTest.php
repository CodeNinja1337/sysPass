<?php
/**
 * sysPass
 *
 * @author    nuxsmin
 * @link      https://syspass.org
 * @copyright 2012-2018, Rubén Domínguez nuxsmin@$syspass.org
 *
 * This file is part of sysPass.
 *
 * sysPass is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * sysPass is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 *  along with sysPass.  If not, see <http://www.gnu.org/licenses/>.
 */

namespace SP\Tests\Services\Install;

use PHPUnit\Framework\TestCase;
use SP\Config\ConfigData;
use SP\Services\Install\InstallData;
use SP\Services\Install\MySQL;
use SP\Storage\Database\MySQLHandler;
use SP\Util\Util;

require_once 'DbTestUtilTrait.php';

/**
 * Class MySQLTest
 *
 * @package SP\Tests\Services\Install
 */
class MySQLTest extends TestCase
{
    use DbTestUtilTrait;

    const DB_NAME = 'syspass_test';

    /**
     * @throws \SP\Storage\Database\DatabaseException
     * @throws \SP\Core\Exceptions\SPException
     */
    public function testCheckDatabaseExist()
    {
        $this->createDatabase(self::DB_NAME);

        $mysql = new MySQL($this->getParams(), new ConfigData());

        $this->assertTrue($mysql->checkDatabaseExist());

        $this->dropDatabase(self::DB_NAME);
    }

    /**
     * @return InstallData
     */
    private function getParams()
    {
        $params = new InstallData();
        $params->setDbAdminUser('root');
        $params->setDbAdminPass('syspass');
        $params->setDbName(self::DB_NAME);
        $params->setDbHost('syspass-db');
        $params->setDbAuthHost(SELF_IP_ADDRESS);
        $params->setDbAuthHostDns(SELF_HOSTNAME);
        $params->setAdminLogin('admin');
        $params->setAdminPass('syspass_admin');
        $params->setMasterPassword('00123456789');
        $params->setSiteLang('en_US');

        return $params;
    }

    /**
     * @throws \SP\Core\Exceptions\SPException
     */
    public function testCheckDatabaseNotExist()
    {
        $mysql = new MySQL($this->getParams(), new ConfigData());

        $this->assertFalse($mysql->checkDatabaseExist());
    }

    /**
     * @throws \SP\Core\Exceptions\SPException
     */
    public function testSetupDbUser()
    {
        $configData = new ConfigData();

        $mysql = new MySQL($this->getParams(), $configData);
        $mysql->setupDbUser();

        $this->assertTrue(preg_match('/sp_\w+/', $configData->getDbUser()) === 1);
        $this->assertNotEmpty($configData->getDbPass());

        $this->dropUser($configData->getDbUser(), SELF_IP_ADDRESS);
        $this->dropUser($configData->getDbUser(), SELF_HOSTNAME);
    }

    /**
     * @throws \SP\Core\Exceptions\SPException
     */
    public function testCreateDatabase()
    {
        $configData = new ConfigData();

        $mysql = new MySQL($this->getParams(), $configData);
        $mysql->setupDbUser();
        $mysql->createDatabase();

        $this->assertTrue($mysql->checkDatabaseExist());

        $this->dropDatabase(self::DB_NAME);
        $this->dropUser($configData->getDbUser(), SELF_IP_ADDRESS);
        $this->dropUser($configData->getDbUser(), SELF_HOSTNAME);
    }

    /**
     * @throws \SP\Core\Exceptions\SPException
     */
    public function testCheckConnection()
    {
        $configData = new ConfigData();

        $mysql = new MySQL($this->getParams(), $configData);
        $mysql->setupDbUser();
        $mysql->createDatabase();
        $mysql->createDBStructure();
        $mysql->checkConnection();

        // Previous steps did not fail then true...
        $this->assertTrue(true);

        $this->dropDatabase(self::DB_NAME);
        $this->dropUser($configData->getDbUser(), SELF_IP_ADDRESS);
        $this->dropUser($configData->getDbUser(), SELF_HOSTNAME);
    }

    /**
     * @throws \SP\Core\Exceptions\SPException
     */
    public function testConnectDatabase()
    {
        $mysql = new MySQL($this->getParams(), new ConfigData());
        $mysql->connectDatabase();

        $this->assertInstanceOf(MySQLHandler::class, $mysql->getDbs());
    }

    /**
     * @throws \SP\Core\Exceptions\SPException
     * @throws \SP\Storage\Database\DatabaseException
     */
    public function testCreateDBUser()
    {
        $mysql = new MySQL($this->getParams(), new ConfigData());
        $mysql->createDBUser('test', Util::randomPassword());

        $num = (int)$mysql->getDbs()
            ->getConnectionSimple()
            ->query('SELECT COUNT(*) FROM mysql.user WHERE `User` = \'test\'')
            ->fetchColumn(0);

        $this->assertEquals(2, $num);

        $this->dropUser('test', SELF_IP_ADDRESS);
        $this->dropUser('test', SELF_HOSTNAME);
    }

    /**
     * @throws \SP\Core\Exceptions\SPException
     */
    public function testRollback()
    {
        $mysql = new MySQL($this->getParams(), new ConfigData());
        $mysql->setupDbUser();
        $mysql->createDatabase();
        $mysql->createDBStructure();
        $mysql->rollback();

        $this->assertFalse($mysql->checkDatabaseExist());
    }

    /**
     * @throws \SP\Core\Exceptions\SPException
     */
    public function testCreateDBStructure()
    {
        $mysql = new MySQL($this->getParams(), new ConfigData());
        $mysql->setupDbUser();
        $mysql->createDatabase();
        $mysql->createDBStructure();

        $this->assertTrue($mysql->checkDatabaseExist());

        $mysql->rollback();
    }
}