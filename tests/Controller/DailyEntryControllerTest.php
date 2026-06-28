<?php

namespace App\Tests\Controller;

use App\Entity\DailyEntry;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\EntityRepository;
use Symfony\Bundle\FrameworkBundle\KernelBrowser;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

final class DailyEntryControllerTest extends WebTestCase
{
    private KernelBrowser $client;
    private EntityManagerInterface $manager;

    /** @var EntityRepository<DailyEntry> */
    private EntityRepository $dailyEntryRepository;
    private string $path = '/daily/entry/';

    protected function setUp(): void
    {
        $this->client = static::createClient();
        $this->manager = static::getContainer()->get('doctrine')->getManager();
        $this->dailyEntryRepository = $this->manager->getRepository(DailyEntry::class);

        foreach ($this->dailyEntryRepository->findAll() as $object) {
            $this->manager->remove($object);
        }

        $this->manager->flush();
    }

    public function testIndex(): void
    {
        $this->client->followRedirects();
        $crawler = $this->client->request('GET', $this->path);

        self::assertResponseStatusCodeSame(200);
        self::assertPageTitleContains('DailyEntry index');

        // Use the $crawler to perform additional assertions e.g.
        // self::assertSame('Some text on the page', $crawler->filter('.p')->first()->text());
    }

    public function testNew(): void
    {
        $this->client->request('GET', sprintf('%snew', $this->path));

        self::assertResponseStatusCodeSame(200);

        $this->client->submitForm('Save', [
            'daily_entry[sleepHours]' => 'Testing',
            'daily_entry[energy]' => 'Testing',
            'daily_entry[stress]' => 'Testing',
            'daily_entry[motivation]' => 'Testing',
            'daily_entry[mood]' => 'Testing',
            'daily_entry[score]' => 'Testing',
            'daily_entry[message]' => 'Testing',
            'daily_entry[advice]' => 'Testing',
        ]);

        self::assertResponseRedirects('/daily/entry');

        self::assertSame(1, $this->dailyEntryRepository->count([]));

        $this->markTestIncomplete('This test was generated');
    }

    public function testShow(): void
    {
        $fixture = new DailyEntry();
        $fixture->setSleepHours('My Title');
        $fixture->setEnergy('My Title');
        $fixture->setStress('My Title');
        $fixture->setMotivation('My Title');
        $fixture->setMood('My Title');
        $fixture->setScore('My Title');
        $fixture->setMessage('My Title');
        $fixture->setAdvice('My Title');

        $this->manager->persist($fixture);
        $this->manager->flush();

        $this->client->request('GET', sprintf('%s%s', $this->path, $fixture->getId()));

        self::assertResponseStatusCodeSame(200);
        self::assertPageTitleContains('DailyEntry');

        // Use assertions to check that the properties are properly displayed.
        $this->markTestIncomplete('This test was generated');
    }

    public function testEdit(): void
    {
        $fixture = new DailyEntry();
        $fixture->setSleepHours('Value');
        $fixture->setEnergy('Value');
        $fixture->setStress('Value');
        $fixture->setMotivation('Value');
        $fixture->setMood('Value');
        $fixture->setScore('Value');
        $fixture->setMessage('Value');
        $fixture->setAdvice('Value');

        $this->manager->persist($fixture);
        $this->manager->flush();

        $this->client->request('GET', sprintf('%s%s/edit', $this->path, $fixture->getId()));

        $this->client->submitForm('Update', [
            'daily_entry[sleepHours]' => 'Something New',
            'daily_entry[energy]' => 'Something New',
            'daily_entry[stress]' => 'Something New',
            'daily_entry[motivation]' => 'Something New',
            'daily_entry[mood]' => 'Something New',
            'daily_entry[score]' => 'Something New',
            'daily_entry[message]' => 'Something New',
            'daily_entry[advice]' => 'Something New',
        ]);

        self::assertResponseRedirects('/daily/entry');

        $fixture = $this->dailyEntryRepository->findAll();

        self::assertSame('Something New', $fixture[0]->getSleepHours());
        self::assertSame('Something New', $fixture[0]->getEnergy());
        self::assertSame('Something New', $fixture[0]->getStress());
        self::assertSame('Something New', $fixture[0]->getMotivation());
        self::assertSame('Something New', $fixture[0]->getMood());
        self::assertSame('Something New', $fixture[0]->getScore());
        self::assertSame('Something New', $fixture[0]->getMessage());
        self::assertSame('Something New', $fixture[0]->getAdvice());

        $this->markTestIncomplete('This test was generated');
    }

    public function testRemove(): void
    {
        $fixture = new DailyEntry();
        $fixture->setSleepHours('Value');
        $fixture->setEnergy('Value');
        $fixture->setStress('Value');
        $fixture->setMotivation('Value');
        $fixture->setMood('Value');
        $fixture->setScore('Value');
        $fixture->setMessage('Value');
        $fixture->setAdvice('Value');

        $this->manager->persist($fixture);
        $this->manager->flush();

        $this->client->request('GET', sprintf('%s%s', $this->path, $fixture->getId()));
        $this->client->submitForm('Delete');

        self::assertResponseRedirects('/daily/entry');
        self::assertSame(0, $this->dailyEntryRepository->count([]));

        $this->markTestIncomplete('This test was generated');
    }
}
