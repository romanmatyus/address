# RM\Address

## Configuration

### ```config.neon```

```
extensions:
	address: RM\Address\DI\AddressExtension

address:
	google:
		country: sk
		language: sk
```

### ```config.local.neon```

```
address:
	google:
		key: AIzaSyAxjvu_LufVeykgNVB5-fFwDbKooARUUPE
```

## In component

```php
$form['street']->setAttribute('data-source', $presenter->link(':Address:Address:address'));
// $form['number'];
// $form['city'];
// $form['zip'];
// $form['country']; // options are array of ['code' => 'Name of state', 'sk' => 'Slovakia']
```
