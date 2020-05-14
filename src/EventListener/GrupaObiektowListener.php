<?php


namespace App\EventListener;


use App\Entity\GrupaObiektow;
use App\Entity\Obiekt;
use App\Entity\Parametr;
use App\Entity\TypParametru;
use Doctrine\ORM\Event\OnFlushEventArgs;
use Doctrine\ORM\PersistentCollection;

class GrupaObiektowListener
{
    public function onFlush(OnFlushEventArgs $event)
    {
        $em = $event->getEntityManager();
        $uow = $em->getUnitOfWork();
        foreach ($uow->getScheduledCollectionUpdates() as $collectionUpdate) {
            /** @var PersistentCollection $collectionUpdate */
            $owner = $collectionUpdate->getOwner();
            if ($owner instanceof GrupaObiektow) {
                $inserted = $collectionUpdate->getInsertDiff();
                $deleted = $collectionUpdate->getDeleteDiff();
                foreach ($inserted as $typ) {
                    /** @var TypParametru $typ */
                    foreach ($owner->getObiekty() as $obiekt) {
                        /** @var Obiekt $obiekt */
                        $parametr = new Parametr();
                        $parametr->setObiekt($obiekt);
                        $parametr->setTyp($typ);
                        $parametr->setValue(null);
                        $obiekt->addParametry($parametr);
                        $uow->computeChangeSet($em->getClassMetadata(Obiekt::class), $obiekt);
                    }
                }
                foreach ($deleted as $typ) {
                    /** @var TypParametru $typ */
                    $query = $em->createQuery('
                        select param from App\Entity\Parametr param
                        inner join param.typ typ 
                        inner join param.obiekt obiekt
                        inner join obiekt.grupa grupa
                        where typ = :typ and grupa = :grupa
                        ')->setParameter('typ', $typ)->setParameter('grupa', $owner);
                    foreach ($query->getResult() as $parametr) {
                        /** @var Parametr $parametr */
                        $em->remove($parametr);
                    }
                }
            }
        }
    }
}