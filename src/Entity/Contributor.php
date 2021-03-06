<?php

namespace App\Entity;

use App\Repository\DecisionRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use Doctrine\Common\Collections\Criteria;
/**
 * @ORM\Entity(repositoryClass="App\Repository\ContributorRepository")
 */
class Contributor
{
    /**
     * @var $decisionRepository DecisionRepository
     */
private  $decisionRepository;
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255)
     * @Assert\Length(
     *     min = 5,
     *      max = 20,
     *      minMessage = "Le login doit être composé au moins par {{ limit }} caractères",
     *      maxMessage = "Le login ne doit pas dépassé {{ limit }} caractères"
     * )
     */
    private $login;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $pwd;

    /**
     * @ORM\ManyToMany(targetEntity="App\Entity\Document", mappedBy="contributors")
     */
    private $documents;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\Decision", mappedBy="contributor")
     */
    private $decisions;
    private $decisionsNT;

    public function __construct()
    {
        $this->documents = new ArrayCollection();
        $this->decisions = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getLogin(): ?string
    {
        return $this->login;
    }

    public function setLogin(string $login): self
    {
        $this->login = $login;

        return $this;
    }

    public function getPwd(): ?string
    {
        return $this->pwd;
    }

    public function setPwd(string $pwd): self
    {
        $this->pwd = $pwd;

        return $this;
    }

/*
    public function getDecisionsNT() {
        $criteria = Criteria::create()
            ->where(Criteria::expr()->eq("isTaken", "0"));
        return $this->decisions->matching($criteria);
    }
*/
    /**
     * @return Collection|Document[]
     */
    public function getDocuments(): Collection
    {
        return $this->documents;
    }

    public function addDocument(Document $document): self
    {
        if (!$this->documents->contains($document)) {
            $this->documents[] = $document;
            $document->addContributor($this);
        }

        return $this;
    }

    public function removeDocument(Document $document): self
    {
        if ($this->documents->contains($document)) {
            $this->documents->removeElement($document);
            $document->removeContributor($this);
        }

        return $this;
    }

    /**
     * @return Collection|Decision[]
     */
    public function getDecisions(): Collection
    {
        return $this->decisions;
    }

    public function addDecision(Decision $decision): self
    {
        if (!$this->decisions->contains($decision)) {
            $this->decisions[] = $decision;
            $decision->setContributor($this);
        }

        return $this;
    }

    public function removeDecision(Decision $decision): self
    {
        if ($this->decisions->contains($decision)) {
            $this->decisions->removeElement($decision);
            // set the owning side to null (unless already changed)
            if ($decision->getContributor() === $this) {
                $decision->setContributor(null);
            }
        }

        return $this;
    }
    /**
     * @return Collection|Decision[]
     */
    public function getDecisionsNT(): Collection
    {
        return $this->decisionsNT;
    }

    public function addDecisionNT(Decision $decision): self
    {
        if (!$this->decisionsNT->contains($decision)) {
            $this->decisionsNT[] = $decision;
            $decision->setContributor($this);
        }

        return $this;
    }

    public function removeDecisionNT(Decision $decision): self
    {
        if ($this->decisionsNT->contains($decision)) {
            $this->decisionsNT->removeElement($decision);
            // set the owning side to null (unless already changed)
            if ($decision->getContributor() === $this) {
                $decision->setContributor(null);
            }
        }

        return $this;
    }

    /**
     * @param Decision[] $decisionsNT
     */
    public function setDecisionsNT($decisionsNT){
        $this->decisionsNT = $decisionsNT;
    }
}
