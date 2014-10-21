<?php
namespace Moip\Resource;

use \stdClass;
use Moip\Http\HTTPRequest;

class Multiorders extends MoipResource
{
    public function initialize()
    {
        $this->data = new stdClass();
        $this->data->ownId = null;
        $this->data->orders = array();
    }
    
    public function addOrder(Orders $order)
    {
        $this->data->orders[] = $order;

        return $this;
    }

    public function create()
    {
        $body = json_encode($this);

        $httpConnection = $this->createConnection();
        $httpConnection->addHeader('Content-Type', 'application/json');
        $httpConnection->addHeader('Content-Length', strlen($body));
        $httpConnection->setRequestBody($body);

        $httpResponse = $httpConnection->execute('/v2/multiorders', HTTPRequest::POST);

        if ($httpResponse->getStatusCode() != 201) {
            throw new \RuntimeException($httpResponse->getStatusMessage(), $httpResponse->getStatusCode());
        }

        return $this->populate(json_decode($httpResponse->getContent()));
    }
    
    public function get($id)
    {
        $httpConnection = $this->createConnection();
        $httpConnection->addHeader('Content-Type', 'application/json');
        
        $httpResponse = $httpConnection->execute('/v2/multiorders/'.$id, HTTPRequest::GET);
        
        if ($httpResponse->getStatusCode() != 200) {
            throw new \RuntimeException($httpResponse->getStatusMessage(), $httpResponse->getStatusCode());
        }
        
        return $this->populate(json_decode($httpResponse->getContent()));
    }
    
    public function getId()
    {
        return $this->getIfSet('id');
    }
    
    public function getOwnId()
    {
        return $this->getIfSet('ownId');
    }
    
    public function getStatus()
    {
        return $this->getIfSet('status');
    }
    
    public function getAmountTotal()
    {
        return $this->getIfSet('total', $this->data->amount);
    }
    
    public function getAmountCurrency()
    {
        return $this->getIfSet('currency', $this->data->amount);
    }
    
    public function getCreatedAt()
    {
        return $this->getIfSet('createdAt');
    }
    
    public function getUpdatedAt()
    {
        return $this->getIfSet('updatedAt');
    }
    
    public function getOrderIterator()
    {
        return new \ArrayIterator($this->getIfSet('orders'));
    }
    
    public function multipayments()
    {
        $payments = new Payment($this->moip);
        $payments->setMultiorder($this);
        
        return $payments;
    }

    protected function populate(stdClass $response)
    {
        $multiorders = clone $this;

        $multiorders->data->id = $response->id;
        $multiorders->data->ownId = $response->ownId;
        $multiorders->data->status = $response->status;
        $multiorders->data->amount = new stdClass();
        $multiorders->data->amount->total = $response->amount->total;
        $multiorders->data->amount->currency = $response->amount->currency;
        $multiorders->data->orders = array();
        
        foreach ($response->orders as $responseOrder) {
            $order = new Orders($multiorders->moip);
            $order->populate($responseOrder);
            
            $multiorders->data->orders[] = $order;
        }
        
        $multiorders->data->createdAt = $response->createdAt;
        $multiorders->data->updatedAt = $response->updatedAt;
        $multiorders->data->_links = $response->_links;
        
        return $multiorders;
    }

    public function setOwnId($ownId)
    {
        $this->data->ownId = $ownId;

        return $this;
    }
}