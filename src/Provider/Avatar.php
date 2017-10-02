<?php
/**
 * Avatar Provider for Faker
 *
 * @author    Platform Team <developer@helpscout.net>
 * @copyright 2016 Help Scout
 */
namespace HelpScout\Specter\Provider;

use Faker\Provider\Base;

/**
 * Class Specter
 *
 * @package HelpScout\Specter
 */
class Avatar extends Base
{
    /**
     * Returns a random and cute robot avatar.
     *
     * @return string url to image
     */
    public function randomRobotAvatar()
    {
        return sprintf(
            'https://robohash.org/%s.png',
            $this->generator->uuid
        );
    }

    /**
     * Return a randomly generate avatar from the Gravatar service.
     *
     * Valid types:
     *  - mm: (mystery-man) a silhouetted outline of a person (does not vary by email hash)
     *  - identicon: a geometric pattern based on an email hash
     *  - monsterid: a generated 'monster' with different colors, faces, etc
     *  - wavatar: generated faces with differing features and backgrounds
     *  - retro: awesome generated, 8-bit arcade-style pixelated faces
     *
     * @param string|null $type Art type for the avatar
     * @param int|null    $size Pixel width dimension
     *
     * @return string url to image
     */
    public function randomGravatar($type = 'identicon', $size = 255)
    {
        // Gravatar style hash with a random email address
        $hash = md5(strtolower(trim($this->generator->companyEmail)));

        return sprintf(
            'https://www.gravatar.com/avatar/%s?d=%s&f=y&s=%d',
            $hash,
            $type,
            $size
        );
    }
}

/* End of file Avatar.php */
