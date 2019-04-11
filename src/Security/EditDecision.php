<?php
namespace App\Security;
use App\Entity\Contributor;
use App\Entity\Decision;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;
class EditDecision extends Voter
{

    const DECIDE = 'DECIDE';
    /**
     * Returns the vote for the given parameters.
     *
     * This method must return one of the following constants:
     * ACCESS_GRANTED, ACCESS_DENIED, or ACCESS_ABSTAIN.
     * @param Symfony\Component\Security\Core\Authentication\Token\TokenInterface $token A TokenInterface instance
     * @param mixed $subject The subject to secure
     * @param array $attributes An array of attributes associated with the method being invoked
     *
     * @return int either ACCESS_GRANTED, ACCESS_ABSTAIN, or ACCESS_DENIED
     */
    public function vote(TokenInterface $token, $subject, array $attributes)
    {
        // Si le voter reçoit  un object autre que Decision , il devra s'abstenir
        if(!$subject instanceof Decision){
            return self::ACCESS_ABSTAIN;
        }
        //Si le voter reçoit des attributs autres que ceux voulus , il devra s'abstenir
        if(!in_array(self::DECIDE,$attributes)){
            return self::ACCESS_ABSTAIN;
        }
        //Sinon le voter doit vérifier que le contributor est conect"
        $contributor = $token->getUser();
        if($contributor instanceof Contributor){
            return self::ACCESS_DENIED;
        }
        /**
         * @var Decision $decision
         */
        $decision = $subject;
        //Si contributor n'est pas le propriétaire de la Decision, Interdire l'accès
        if($decision !==$subject->getOwner()){ //A vérifier
            return self::ACCESS_DENIED;
        }
        return self::ACCESS_GRANTED;
    }
}