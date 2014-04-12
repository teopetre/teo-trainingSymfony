<?php

namespace Acme\TrainingBundle\Tests\Services;


use Acme\TrainingBundle\Entity\CustomerPermission;
use Acme\TrainingBundle\Entity\CustomerRole;
use Acme\TrainingBundle\Services\CustomerRoleHandler;
use JMS\Serializer\SerializerBuilder;
use Symfony\Bundle\FrameworkBundle\Tests\Functional\WebTestCase;

/**
 * Class CustomerRoleHandlerTest
 * @package Acme\TrainingBundle\Tests\Services
 */
class CustomerRoleHandlerTest extends WebTestCase
{
    /** @var  CustomerRoleHandler */
    protected $customerRoleHandler;

    protected static $customerRoles;

    public function setUp()
    {
        $this->customerRoleHandler = new CustomerRoleHandler();
        $this->customerRoleHandler->setSerializer(
          SerializerBuilder::create()->build()
        );
        self::$customerRoles = $customerRoles =
          array(
            array(
              'id' => 1,
              'name' => 'myallegiant',
              'customerPermission' => array(
                array(
                  'id' => 16,
                  'name' => 'Apply a voucher as a form of payment'
                ),
                array(
                  'id' => 7,
                  'name' => 'Add Bags to a Booking'
                ),
                array(
                  'id' => 8,
                  'name' => 'Add Priority Boarding to Booking'
                ),
                array(
                  'id' => 6,
                  'name' => 'Add Seats to a Booking'
                ),
                array(
                  'id' => 11,
                  'name' => 'Add SSR to a Booking'
                ),
              ),
            ),
            array(
              'id' => 2,
              'name' => 'call_center_agent',
              'customerPermission' => array(
                array(
                  'id' => 18,
                  'name' => 'Find Voucher'
                ),
                array(
                  'id' => 19,
                  'name' => 'Find Customers'
                ),
              ),
            ),
          );
    }

    public function testDeserializeData()
    {
        // Deserialize malformed data.
        $customerRoles = array(
          array(
            '' => 1,
            'wrong_name' => 'call_center_agent',
            'cp' => array(
              array(
                'id' => 18,
                'name' => 'Find Voucher'
              ),
              array(
                'id' => 19,
                'name' => 'Find Customers'
              ),
            ),
          ),
          array(),
        );
        $jsonCR = json_encode($customerRoles);
        $customerRoles = $this->customerRoleHandler->deserializeData($jsonCR);
        $this->assertContainsOnlyInstancesOf(
          'Acme\TrainingBundle\Entity\CustomerRole',
          $customerRoles
        );
        $this->assertCount(2, $customerRoles);

        $jsonCR = json_encode(self::$customerRoles);
        $customerRoles = $this->customerRoleHandler->deserializeData($jsonCR);
        $this->assertContainsOnlyInstancesOf(
          'Acme\TrainingBundle\Entity\CustomerRole',
          $customerRoles
        );
    }

    public function testSerializeData()
    {
        // Create array of CustomerRole objects from $customerRoles data.
        $customerRolesObjects = array();
        foreach (self::$customerRoles as $customerRole) {
            $customerRoleObject = new CustomerRole();
            $customerRoleObject->setId($customerRole['id']);
            $customerRoleObject->setName($customerRole['name']);

            $customerPermissionsObjects = array();
            foreach ($customerRole['customerPermission'] as $customerPermission) {
                $customerPermissionObject = new CustomerPermission();
                $customerPermissionObject->setId($customerPermission['id']);
                $customerPermissionObject->setName($customerPermission['name']);

                $customerPermissionsObjects[] = $customerPermissionObject;
            }
            $customerRoleObject->setCustomerPermission($customerPermissionsObjects);

            $customerRolesObjects[] = $customerRoleObject;
        }

        $serializedCustomRoles = $this->customerRoleHandler->serializeData($customerRolesObjects);
        $this->assertJsonStringEqualsJsonString(json_encode(self::$customerRoles), $serializedCustomRoles);
    }

}