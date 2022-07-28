# TYPO3 symfony messenger adapter
Integrates Symfony Messenger into TYPO3


## Available commands

This extension makes the following commands available:

```
 messenger
  messenger:stop-workers                      Stop workers after their current message
  messenger:setup-transports                  Prepare the required infrastructure for the transport
  messenger:consume                           Consume messages
  messenger:failed:retry                      Retry one or more messages from the failure transport
  messenger:failed:show                       Show one or more messages from the failure transport
  messenger:failed:remove                     Remove given messages from the failure transport
```

## Integration guide

Here is an example which uses a sql table.

Require the `wapplersystems/messenger` package in your composer.json.

Create these files in your own extension:

Configuration/Services.php
```
namespace TYPO3\CMS\Core;

use Symfony\Component\DependencyInjection\Compiler\PassConfig;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;
use WapplerSystems\Messenger\DependencyInjection\MessengerExtensionConfigPass;


return static function (ContainerConfigurator $container, ContainerBuilder $containerBuilder) {

    $containerBuilder->addCompilerPass(new MessengerExtensionConfigPass('<your_ext_key>'),PassConfig::TYPE_BEFORE_OPTIMIZATION, 91);

};
```

Configuration/Services.yaml
```
  Vendor\YourExt\Service\FoobarService:
    public: true
    arguments:
      $bus: '@messenger.bus.default'
```



ext_tables.sql
```
CREATE TABLE tx_yourext_foobar
(
	uid            int(11)                  NOT NULL auto_increment,
	failed         tinyint(4)   DEFAULT '0' NOT NULL,
	handled        tinyint(4)   DEFAULT '0' NOT NULL,
	envelope       text,
	delivered_at   int(11)      DEFAULT NULL,

	PRIMARY KEY (uid)
);
```

Configuration/Messenger.yaml
```
transports:
  foobar:
    dsn: 'foobar-transport://'
    failure_transport: false
    options: { }
routing:
  'Vendor\YourExt\Job\Foobar':
    senders:
      - messenger.transport.foobar
```

FoobarTransportFactory.php
```
namespace Vendor\YourExt\Messenger\Transport\FoobarTransportFactory;

use Vendor\YourExt\Messenger\FoobarTransport;
use Symfony\Component\Messenger\Transport\Serialization\SerializerInterface;
use Symfony\Component\Messenger\Transport\TransportFactoryInterface;
use Symfony\Component\Messenger\Transport\TransportInterface;
use TYPO3\CMS\Core\Utility\GeneralUtility;

class FoobarTransportFactory implements TransportFactoryInterface
{
    public function createTransport(string $dsn, array $options, SerializerInterface $serializer): TransportInterface
    {
        return GeneralUtility::makeInstance(FoobarTransport::class);
    }

    public function supports(string $dsn, array $options): bool
    {
        return str_starts_with($dsn, 'foobar-transport://');
    }
}
```

Foobar.php
```
class Foobar
{

}
```

FoobarHandler.php
```

use Vendor\YourExt\Job\Foobar;
use Vendor\YourExt\Service\ReportsService;
use Symfony\Component\Messenger\Handler\MessageHandlerInterface;
use TYPO3\CMS\Core\Utility\GeneralUtility;

class FoobarHandler implements MessageHandlerInterface {
{
    public function __invoke(Foobar $foobar)
    {

    }
}
```

FoobarService.php
```
use Symfony\Component\Messenger\MessageBusInterface;
use Vendor\YourExt\Job\Foobar;

class FoobarService
{
    public function __construct(MessageBusInterface $bus)
    {
        $this->bus = $bus;
    }

    public function createJob() {
        $foobar = new Foobar();
        $this->bus->dispatch($foobar);
    }
}
```

ReportJobTransport.php
```
use Symfony\Component\Messenger\Envelope;
use Symfony\Component\Messenger\Stamp\TransportMessageIdStamp;
use Symfony\Component\Messenger\Transport\Serialization\PhpSerializer;
use Symfony\Component\Messenger\Transport\Serialization\SerializerInterface;
use Symfony\Component\Messenger\Transport\TransportInterface;
use TYPO3\CMS\Core\Database\Connection;
use TYPO3\CMS\Core\Database\ConnectionPool;
use TYPO3\CMS\Core\Utility\GeneralUtility;

class FoobarTransport implements TransportInterface {


    public function __construct(SerializerInterface $serializer = null)
    {
        $this->serializer = $serializer ?? new PhpSerializer();
    }

    public function get(): iterable
    {
        $queryBuilder = GeneralUtility::makeInstance(ConnectionPool::class)->getQueryBuilderForTable('tx_yourext_foobar');
        $row = $queryBuilder->select('*')
            ->from('tx_yourext_foobar')
            ->where($queryBuilder->expr()->eq('handled',0))
            ->setMaxResults(1)
            ->execute()
            ->fetchAssociative();

        if ($row === false) {
            return [];
        }

        $envelope = $this->serializer->decode([
            'body' => $row['envelope'],
        ]);

        return [$envelope->with(new TransportMessageIdStamp($row['uid']))];
    }

    public function ack(Envelope $envelope): void
    {
        $stamp = $envelope->last(TransportMessageIdStamp::class);
        if (!$stamp instanceof TransportMessageIdStamp) {
            throw new \LogicException('No TransportMessageIdStamp found on the Envelope.');
        }

        GeneralUtility::makeInstance(ConnectionPool::class)
            ->getConnectionForTable('tx_yourext_foobar')
            ->update(
                'tx_yourext_foobar',
                ['handled' => 1, 'delivered_at' => time()],
                ['uid' => (int)$stamp->getId()],
                [Connection::PARAM_INT]
            );

    }

    public function reject(Envelope $envelope): void
    {
        $stamp = $envelope->last(TransportMessageIdStamp::class);
        if (!$stamp instanceof TransportMessageIdStamp) {
            throw new \LogicException('No TransportMessageIdStamp found on the Envelope.');
        }

        $queryBuilder = GeneralUtility::makeInstance(ConnectionPool::class)->getQueryBuilderForTable('tx_yourext_foobar');
        $queryBuilder->delete('tx_yourext_foobar')
            ->where($queryBuilder->expr()->eq('uid',$queryBuilder->createNamedParameter($stamp->getId(),\PDO::PARAM_INT)))
            ->executeQuery();

    }

    public function send(Envelope $envelope): Envelope
    {
        $encodedMessage = $this->serializer->encode($envelope);

        GeneralUtility::makeInstance(ConnectionPool::class)->getConnectionForTable('tx_yourext_foobar')
            ->insert(
                'tx_yourext_foobar',
                ['envelope' => $encodedMessage['body'], 'delivered_at' => null],
                ['handled' => 0]
            );
        $uid = (int)GeneralUtility::makeInstance(ConnectionPool::class)->getConnectionForTable('tx_yourext_foobar')->lastInsertId('tx_yourext_foobar');

        return $envelope->with(new TransportMessageIdStamp($uid));
    }

}

```
