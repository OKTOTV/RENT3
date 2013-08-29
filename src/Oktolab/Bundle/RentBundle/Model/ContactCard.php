<?php
/**
 * (c) Okto
 * @package ContactCardPlugin
 */
namespace Oktolab\Bundle\RentBundle\Model;
/**
 * A ContactCard is a specific standardized subset of personal data that's
 * derived from some user data base or directory server. Using ContactCards
 * you are able to abstract the backend and provide a generic way to handle
 * both person's data and their visualization (using ContactCardRenderers).
 *
 * ContactCards use a globally unique ID (guid) that is defined by the
 * directory that is used. This may be some simple integer ID or a more
 * complex string value.
 *
 * @author dh
 * @package ContactCardPlugin
 *
 */
class ContactCard
{
  /**
   * The ContactCard implementation version.
   *
   * @var int
   */
  const VERSION = 2;

  /**
   * ContactCard type representing a person
   *
   * @var string
   */
  const TYPE_PERSON = 'person';

  /**
   * ContactCard type representing a user
   *
   * @var string
   */
  const TYPE_USER = 'user';

  /**
   * ContactCard type representing an organization
   *
   * @var string
   */
  const TYPE_ORGANIZATION = 'organization';

  /**
   * ContactCard type representing a group
   *
   * @var string
   */
  const TYPE_GROUP = 'group';

  /**
   * Array containing the names of all available types.
   *
   * @var array
   */
  protected $typeNames = array(
    self::TYPE_PERSON       => self::TYPE_PERSON,
    self::TYPE_USER         => self::TYPE_USER,
    self::TYPE_ORGANIZATION => self::TYPE_ORGANIZATION,
    self::TYPE_GROUP        => self::TYPE_GROUP,
  );

  /**
   * Array containing the names of all available attributes.
   *
   * @var array
   */
  protected $attributeNames = array(
    'display_name'              => 'display_name',
    'organization'              => 'organization',
    'organizational_identifier' => 'organizational_identifier',
    'email'                     => 'email',
    'telephone'                 => 'telephone',
    'image_type'                => 'image_type',
    'image_data'                => 'image_data',
    'permissions'               => 'permissions',
  );

  /**
   * The GUID of this contact.
   *
   * @var string
   */
  protected $guid = null;

  /**
   * The type of this ContactCard.
   *
   * @var string
   */
  protected $type = null;

  /**
   * An array holding all the attributes of this contact.
   *
   * @var array
   */
  protected $attributes = array();

  /**
   * Create a new ContactCard object.
   *
   * @throws InvalidArgumentException if invalid type or data are being used
   *
   * @param string $guid          The unique id for this contact
   * @param string $displayName   The name to display
   * @param string $type          The ContactCard type
   * @param array $data           Associative array containing additional attributes
   */
  public function __construct($guid, $displayName, $type, array $data = array())
  {
    $this->guid = (string) $guid;

    foreach ($this->attributeNames as $name) {
      // init all values as empty
      $this->attributes[$name] = '';
    }

    $this->setDisplayName($displayName);
    $this->setType($type);

    foreach ($data as $name => $value) {
      $this->setAttribute($name, $value);
    }
    $now = new DateTime();
    $this->attributes['created_at'] = $this->attributes['updated_at'] = $now->format('c');
  }

  /**
   * Return the contact's GUID.
   *
   * @return string
   */
  public function getGuid()
  {
    return $this->guid;
  }

  /**
   * Get the ContactCard implementation version.
   *
   * @return int  The ContactCard version number
   */
  public function getVersion()
  {
    return $this->version;
  }

  /**
   * Set an attribute value.
   *
   * @throws InvalidArgumentException if the attribute name does not exist
   *
   * @param string $name
   * @param string $value
   * @return ContactCard  Fluent interface
   */
  public function setAttribute($name, $value)
  {
    if (isset($this->attributeNames[$name])) {
      if ('permissions' == $name) {
        $this->attributes[$name] = (array) $value;
      } else {
        $this->attributes[$name] = (string) $value;
      }
      $now = new DateTime();
      $this->attributes['updated_at'] = $now->format('c');
    } else {
      throw new InvalidArgumentException(sprintf('Attribute %s does not exist', $name));
    }
    return $this;
  }

  /**
   * Get an attribute value.
   *
   * @throws InvalidArgumentException if the attribute name does not exist
   *
   * @param string $name
   * @return string
   */
  public function getAttribute($name)
  {
    if (!isset($this->attributeNames[$name])) {
      throw new InvalidArgumentException(sprintf('Attribute %s does not exist', $name));
    }
    return (isset($this->attributes[$name]) ? $this->attributes[$name] : '');
  }

  /**
   * Get an array containing all attributes.
   *
   * @return array
   */
  public function getAttributes()
  {
    return $this->attributes;
  }

  /**
   * Check if the given attribute name is defined.
   *
   * @param string $name
   * @return boolean
   */
  public function hasAttributeName($name)
  {
    return isset($this->attributeNames[$name]);
  }

  /**
   * Get an array containing the attribute names.
   *
   * @return array
   */
  public function getAttributeNames()
  {
    return array_keys($this->attributeNames);
  }

  /**
   * Set the display_name attribute.
   *
   * @param string $displayName   The name to display for the contact
   * @return ContactCard          Fluent interface
   */
  public function setDisplayName($displayName)
  {
    return $this->setAttribute('display_name', $displayName);
  }

  /**
   * Set the organization attribute.
   *
   * @param string $organization  The organization name
   * @return ContactCard          Fluent interface
   */
  public function setOrganization($organization)
  {
    return $this->setAttribute('organization', $organization);
  }

  /**
   * Set the organizational_identifier attribute.
   *
   * @param string $organizationalIdentifier  Identifier (e.g. account name) in the organization
   * @return ContactCard                      Fluent interface
   */
  public function setOrganizationalIdentifier($organizationalIdentifier)
  {
    return $this->setAttribute('organizational_identifier', $organizationalIdentifier);
  }

  /**
   * Set the email attribute.
   *
   * @param string $email   Email address
   * @return ContactCard    Fluent interface
   */
  public function setEmail($email)
  {
    return $this->setAttribute('email', $email);
  }

  /**
   * Set the image attribute.
   *
   * @param string $imageContentType  Content type (e.g. 'image/png')
   * @param string $imageData         Raw binary image data
   * @return ContactCard              Fluent interface
   */
  public function setImage($imageContentType, $imageData)
  {
    $this->setAttribute('image_type', $imageContentType);
    $this->setAttribute('image_data', base64_encode($imageData));
    return $this;
  }

  /**
   * Get the display_name attribute.
   *
   * @return string   The name to display
   */
  public function getDisplayName()
  {
    return $this->getAttribute('display_name');
  }

  /**
   * Get the organization attribute.
   *
   * @return string   The organization name
   */
  public function getOrganization()
  {
    return $this->getAttribute('organization');
  }

  /**
   * Get the organizational_identifier attribute.
   *
   * @return string   Identifier (e.g. account name) in the organization
   */
  public function getOrganizationalIdentifier()
  {
    return $this->getAttribute('organizational_identifier');
  }

  /**
   * Get the email attribute.
   *
   * @return string   Email address
   */
  public function getEmail()
  {
    return $this->getAttribute('email');
  }

  public function getTelephone()
  {
    if (isset($this->attributes['telephone'])) {
      return $this->attributes['telephone'];
    }

    return null;
  }

  /**
   * Get the image_type attribute. Contains the content-type of the image.
   *
   * @return string   Image content-type
   */
  public function getImageContentType()
  {
    return $this->getAttribute('image_type');
  }

  /**
   * Get the base64 encoded image data.
   *
   * @return string   Image data in base64 format
   */
  public function getImageData64()
  {
    return $this->getAttribute('image_data');
  }

  /**
   * Set the type the ContactCard should represent. Available types
   * are also specified as TYPE_* class constants.
   *
   * @throws InvalidArgumentException if the type does not exist
   * @see ContactCard::getTypeNames()
   *
   * @param string $type
   * @return ContactCard  Fluent interface
   */
  public function setType($type)
  {
    if (isset($this->typeNames[$type])) {
      $this->type = (string) $type;
    } else {
      throw new InvalidArgumentException(sprintf('Type %s does not exist', $type));
    }
    return $this;
  }

  /**
   * Get the type of this ContactCard.
   *
   * @return string
   */
  public function getType()
  {
    return $this->type;
  }

  /**
   * Get an array containing the available type names.
   *
   * @return array
   */
  public function getTypeNames()
  {
    return array_keys($this->typeNames);
  }

  /**
   * Get the creation datetime of this ContactCard in full ISO format.
   *
   * @return string
   */
  public function getCreatedAt()
  {
    return $this->getAttribute('created_at');
  }

  /**
   * Get the update datetime of this ContactCard in full ISO format.
   *
   * @return string
   */
  public function getUpdatedAt()
  {
    return $this->getAttribute('updated_at');
  }

  /**
   * Check if this ContactCard matches the ContactCard given as argument.
   * Compares all content fields but not the created_at and updated_at
   * values.
   *
   * @param ContactCard $card
   * @return boolean
   */
  public function equals(ContactCard $card)
  {
    if ($this->getGuid() != $card->getGuid()) {
      return false;
    }
    if ($this->getType() != $card->getType()) {
      return false;
    }
    foreach ($this->attributeNames as $name) {
      if ($this->getAttribute($name) != $card->getAttribute($name)) {
        return false;
      }
    }
    return true;
  }

  /**
   * returns the permissions given in this object
   * @return array   array(perm1 => perm1, perm2 => perm2)
   */
  public function getPermissions()
  {
    if (!isset($this->attributeNames['permissions'])) {
      // BC with version 1 ContactCards
      return array();
    }

    $permissions = $this->getAttribute('permissions');
    return is_array($permissions) ? $permissions : array();
  }

  /**
   * returns wheter $permission exists or not
   *
   * @param string $permission
   * @return bool
   */
  public function hasPermission($permission)
  {
    $permissions = $this->getPermissions();
    return isset($permissions[$permission]);
  }
}
