<?php

namespace ApiBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use JMS\Serializer\Annotation as JMS;

/**
 * Issue
 *
 * @ORM\Table(name="message")
 * @ORM\Entity(repositoryClass="ApiBundle\Repository\IssueRepository")
 * 
 * @JMS\ExclusionPolicy("all")
 * @JMS\AccessorOrder("custom", custom = {"id", "title", "content"})
 */
class Message
{
    /**
     * @var int
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     * 
     * @JMS\Expose
     * @JMS\Groups({"default"})
     */
    private $id;

    /**
     * @var string
     *
     * @ORM\Column(name="title", type="string", length=255)
     * 
     * @JMS\Expose
     * @JMS\Groups({"default"})
     */
    private $title;

    /**
     * @var string
     *
     * @ORM\Column(name="content", type="text")
     * 
     * @JMS\Expose
     * @JMS\Groups({"default"})
     */
    private $content;

    /**
     * @var string
     *
     * @ORM\ManyToOne(targetEntity="Message", inversedBy="childMessages")
     */
    private $parentMessage;
    
    /**
     * @var string
     *
     * @ORM\OneToMany(targetEntity="Message", mappedBy="parentMessage")
     * @JMS\Expose
     * @JMS\Groups({"thread"})
     * @JMS\SerializedName("children")
     */
    private $childMessages;
    
    /**
     * @JMS\VirtualProperty()
     * @JMS\SerializedName("parent")
     */
    public function getParentId()
    {
        return $this->getParentMessage()?$this->getParentMessage()->getId():null;
    }
    
    
    /**
     * Get id
     *
     * @return integer 
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set title
     *
     * @param string $title
     * @return Issue
     */
    public function setTitle($title)
    {
        $this->title = $title;

        return $this;
    }

    /**
     * Get title
     *
     * @return string 
     */
    public function getTitle()
    {
        return $this->title;
    }

    /**
     * Set content
     *
     * @param string $content
     * @return Issue
     */
    public function setContent($content)
    {
        $this->content = $content;

        return $this;
    }

    /**
     * Get content
     *
     * @return string 
     */
    public function getContent()
    {
        return $this->content;
    }
    /**
     * Constructor
     */
    public function __construct()
    {
        $this->childMessages = new \Doctrine\Common\Collections\ArrayCollection();
    }

    /**
     * Set parentMessage
     *
     * @param \ApiBundle\Entity\Message $parentMessage
     * @return Message
     */
    public function setParentMessage(\ApiBundle\Entity\Message $parentMessage = null)
    {
        $this->parentMessage = $parentMessage;

        return $this;
    }

    /**
     * Get parentMessage
     *
     * @return \ApiBundle\Entity\Message 
     */
    public function getParentMessage()
    {
        return $this->parentMessage;
    }

    /**
     * Add childMessages
     *
     * @param \ApiBundle\Entity\Message $childMessages
     * @return Message
     */
    public function addChildMessage(\ApiBundle\Entity\Message $childMessages)
    {
        $this->childMessages[] = $childMessages;

        return $this;
    }

    /**
     * Remove childMessages
     *
     * @param \ApiBundle\Entity\Message $childMessages
     */
    public function removeChildMessage(\ApiBundle\Entity\Message $childMessages)
    {
        $this->childMessages->removeElement($childMessages);
    }

    /**
     * Get childMessages
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getChildMessages()
    {
        return $this->childMessages;
    }
}
