<!DOCTYPE html>
<html>

<head>
    <title>Notification Email</title>
</head>

<body>
    <h1>{{ $mailText['message'] }}</h1>

    <?php if (!empty($mailText['contractId'])) : ?>
        <p>{{ $mailText['contractId'] }}</p>
    <?php endif; ?>

    <?php if (!empty($mailText['event'])) : ?>
        <p>{{ $mailText['event'] }}</p>
    <?php endif; ?>

    <?php if (!empty($mailText['action'])) : ?>
        <p>{{ $mailText['action'] }}</p>
    <?php endif; ?>

    <?php if (!empty($mailText['transactionHeadIds'])) : ?>
        <?php foreach ($mailText['transactionHeadIds'] as $transactionHeadId) : ?>
            <p>{{ $transactionHeadId }}</p>
        <?php endforeach; ?>
    <?php endif; ?>
</body>

</html>
