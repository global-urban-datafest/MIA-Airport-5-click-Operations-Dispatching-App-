<?php
class MessageType{
    public $type;
    public $typedescription;

    /**
     * @return mixed
     */
    public function getType()
    {
        return $this->type;
    }

    /**
     * @return mixed
     */
    public function getTypeDescription()
    {
        return $this->typedescription;
    }
}
class User {
    public $id;
    public $name;
    public $barcodeID;
    public $pin;
    public $type;
    public $lastlogin;
    public $ip;

    /**
     * @return mixed
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @return mixed
     */
    public function getPin()
    {
        return $this->pin;
    }

    /**
     * @return mixed
     */
    public function getType()
    {
        return $this->type;
    }

    /**
     * @return mixed
     */
    public function getLastlogin()
    {
        return $this->lastlogin;
    }

    /**
     * @param mixed $lastlogin
     */
    public function setLastlogin($lastlogin)
    {
        $this->lastlogin = $lastlogin;
    }

    /**
     * @return mixed
     */
    public function getIp()
    {
        return $this->ip;
    }

    /**
     * @param mixed $ip
     */
    public function setIp($ip)
    {
        $this->ip = $ip;
    }

    /**
     * @return mixed
     */
    public function getId()
    {
        return $this->id;
    }

}
class Message
{
    private $core;

    private $sender, $first_reader;
    private $type, $typeid;
    private $message;
    private $timestamp;
    private $unread = true;
    private $readtime;

    function __construct()
    {
        $this->typeid = $this->type;
    }

    function getType()
    {
        if (!$this->type instanceof MessageType)
            $this->typeid = $this->type;
            if ($this->core != null) {
                $messageType = $this->core->execute("SELECT * FROM `messagetypes` WHERE `type` = $this->type");
                $messageType = $messageType->fetchAll(PDO::FETCH_CLASS, "MessageType");
            }

        $this->type = $messageType[0];

        if ($this->type != null) {
            return $this->type->getTypeDescription();
        } else {

            return "";
        }
    }

    /**
     * @return mixed
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @return mixed
     */
    public function getSender()
    {
        return $this->sender;
    }

    /**
     * @return mixed
     */
    public function getSenderName()
    {
        if ($this->core != null) {
            $name = $this->core->execute("SELECT * FROM `users` WHERE `id` = $this->sender");
            $name = $name->fetchAll(PDO::FETCH_CLASS, "User");
        }
        $this->sender = $name[0];

        return $this->sender->getName();
    }

    /**
     * @return mixed
     */
    public function getReaderName()
    {
        if ($this->core != null) {
            $name = $this->core->execute("SELECT * FROM `users` WHERE `id` = $this->first_reader");
            $name = $name->fetchAll(PDO::FETCH_CLASS, "User");
        }
        $this->reader = $name[0];
        if ($this->reader != null) {
            return $this->reader->getName();
        } else {
            return "Unread";
        }
    }


    /**
     * @param mixed $reader
     */
    public function setReader($reader)
    {
        $this->reader = $reader;
    }


    /**
     * @return mixed
     */
    public function getMessage()
    {
        return $this->message;
    }

    /**
     * @return mixed
     */
    public function getTimestamp()
    {
        if($this->getRead()){
            $timestamp = '<span style="color: darkred">' . date('m/d g:i A', strtotime($this->timestamp)) . '</span>';
        } else {
            $timestamp = '<span style="color: green">' . date('m/d g:i A', strtotime($this->timestamp)) . '</span>';
        }
        return $timestamp;
    }


    /**
     * @return mixed
     */
    public function getRead()
    {


        return $this->unread;
    }
    public function getUnread()
{


    return !$this->unread;
}

    /**
     * @param mixed $unread
     */
    public function setUnread($unread)
    {
        $this->unread = $unread;
    }

    /**
     * @return mixed
     */
    public function getReadtime()
    {
        return date('g:i A', strtotime($this->readtime));
    }

    /**
     * @param mixed $readtime
     */
    public function setReadtime($readtime)
    {
        $this->readtime = $readtime;
    }

    /**
     * @param mixed $core
     */
    public function setCore($core)
    {
        $this->core = $core;
    }


    public function insert($db){

        $message = $db->prepare("UPDATE `messages` SET id=:id, sender=:sender, first_reader=:reader,
                    type=:type, message=:message, timestamp=:timestamp, unread=:unread, readtime=CURRENT_TIMESTAMP
                    WHERE id = :id;");
        $message->bindParam(':id', $this->getId(), PDO::PARAM_INT);
        $message->bindParam(':sender', $this->getSender(), PDO::PARAM_INT);
        $message->bindParam(':reader', $this->reader, PDO::PARAM_INT);
        $message->bindParam(':type', $this->typeid, PDO::PARAM_INT);
        $message->bindParam(':message', $this->getMessage(), PDO::PARAM_STR);
        $message->bindParam(':timestamp', date('Y-m-d G:i:s', strtotime($this->timestamp)), PDO::PARAM_BOOL);
        $message->bindParam(':unread', $this->getRead(), PDO::PARAM_BOOL);
        $message->bindParam(':readtime', $this->getReadtime(), PDO::PARAM_STR);
        $message->execute();

    }

    /**
     * @return mixed
     */
    public function getFirstReader()
    {
        return $this->first_reader;
    }

}

?>