<?php

class CSqlite
{
  protected array $aConf = [
    'vPath' => '/home/tarik/projetos/quazymodo/app/writable/db',
    'vSearch' => '#(.+\.sqlite|.+\.db)$#',
    'vPwdFile' => '/home/tarik/projetos/quazymodo/app/writable/adminer/CSqlite.pwd',
  ];

  public function __construct(array $aOpt = [])
  {
    $this->aConf = $aOpt + $this->aConf;
  }

  public function loginForm(): string
  {
    $aTxt = [
      'vPassword' => Adminer\lang('Password'),
      'vDatabase' => Adminer\lang('Database'),
      'vPermanent' => Adminer\lang('Permanent login'),
    ];

    $vInput = $this->_mBuildDropDown();

    echo "<form method='post'>\n";
    echo "  <input type='hidden' name='auth[driver]' value='sqlite'>\n";
    echo "  <table class='layout'>\n";
    echo "    <tr><th>{$aTxt['vDatabase']} (sqlite):</th><td>{$vInput}</td></tr>\n";
    echo "    <tr><th>{$aTxt['vPassword']}:</th><td><input type='password' name='auth[password]'></td></tr>\n";
    echo "  </table>\n";
    echo "  <p><input type='submit' value='Login'> ";
    echo "<label><input type='checkbox' name='auth[permanent]' value='1'>{$aTxt['vPermanent']}</label></p>\n";
    echo "</form>\n";

    return 'replace';
  }

  protected function _mBuildDropDown(): string
  {
    $vOption = '';
    $aFile = $this->_mScanDbFile();
    $vSelected = $_POST['auth']['db'] ?? '';

    foreach ($aFile as $vFile) {
      $vSelect = ($vSelected === $vFile) ? ' selected' : '';
      $vTxt = str_replace($this->aConf['vPath'], '', $vFile);
      $vOption .= "<option value='" . htmlspecialchars($vFile, ENT_QUOTES, 'UTF-8') . "'{$vSelect}>" . htmlspecialchars($vTxt, ENT_QUOTES, 'UTF-8') . "</option>";
    }

    if ($vOption === '') {
      return "<input type='text' name='auth[db]'>";
    }

    return "<select name='auth[db]'>{$vOption}</select>";
  }

  protected function _mScanDbFile(): array
  {
    if (!is_dir($this->aConf['vPath'])) {
      return [];
    }

    $oDir = new RecursiveDirectoryIterator($this->aConf['vPath'], RecursiveDirectoryIterator::SKIP_DOTS);
    $oIterator = new RecursiveIteratorIterator($oDir);
    $oFile = new RegexIterator($oIterator, $this->aConf['vSearch'], RegexIterator::GET_MATCH);
    $aFound = [];

    foreach ($oFile as $vFile) {
      $aFound = array_merge($aFound, $vFile);
    }

    sort($aFound);
    return array_values(array_unique($aFound));
  }

  public function databases(): array
  {
    $aDatabase = [];

    foreach ($this->_mScanDbFile() as $vFile) {
      $vTxt = str_replace($this->aConf['vPath'], '', $vFile);
      $aDatabase[$vFile] = $vTxt;
    }

    return $aDatabase;
  }

  protected function _credentials(): array
  {
    $vPwd = Adminer\get_password();

    if (file_exists($this->aConf['vPwdFile'])) {
      $vPwdHash = file_get_contents($this->aConf['vPwdFile']);

      if (!is_string($vPwdHash) || $vPwdHash === '') {
        throw new Error("Can't read password file ({$this->aConf['vPwdFile']})");
      }

      if (password_verify((string) $vPwd, $vPwdHash)) {
        $vPwd = '';
      }
    } elseif (!$vPwd) {
      $vPwd = 'missing';
    } else {
      $vPwdHash = password_hash((string) $vPwd, PASSWORD_DEFAULT);

      if ($vPwdHash === false) {
        throw new Error('Failed to hash password.');
      }

      $pwdDir = dirname($this->aConf['vPwdFile']);
      if (!is_dir($pwdDir) && !mkdir($pwdDir, 0775, true) && !is_dir($pwdDir)) {
        throw new Error("Can't create directory ({$pwdDir})");
      }

      if (file_put_contents($this->aConf['vPwdFile'], $vPwdHash) === false) {
        throw new Error("Can't write password file ({$this->aConf['vPwdFile']})");
      }

      $vPwd = '';
    }

    return [Adminer\SERVER, $_GET['username'], (string) $vPwd];
  }

  public function login($login, $vPwd): bool
  {
    return $vPwd !== '';
  }

  public function credentials(): array
  {
    try {
      return $this->_credentials();
    } catch (Error $e) {
      $this->_mError($e->getMessage());
    }

    return [Adminer\SERVER, $_GET['username'] ?? '', 'missing'];
  }

  protected function _mError(string $vMsg): void
  {
    die("<h3 style='color: red;'>Error occurred<br>{$vMsg}</h3>");
  }
}
