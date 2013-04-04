EC-CUBEアフィリナビトラッキングプラグイン
=========================================
アフィリナビPRO専用の試作品です。

インストール方法
----------------
1. [プロジェクトをダウンロード](https://github.com/zenith6/eccube-antracking/archive/master.tar.gz "eccube-antracking-master.tar.gz")します。

    ```bash
    # プロジェクトをダウンロードする
    wget https://github.com/zenith6/eccube-antracking/archive/master.tar.gz
    ```
2. ダウンロードした書庫をインストール可能なディレクトリレイアウトに修正します。
    eccube-antracking-master 以下のファイルが、書庫のルートディレクトリへ配置されるようにして下さい。

    ```bash
    # 書庫を展開する
    tar xzvf master.tar.gz
    # 展開先の eccube-antracking-master に移動する
    cd eccube-antracking-master
    # 以下のファイルを再圧縮する
    tar czvf ../AnTracking.tar.gz *
    ```

3. EC-CUBEの管理画面にログインし、オーナーズストア > プラグインの管理 と進みます。
4. プラグイン登録にダウンロードしたファイルを指定しインストールします。
5. アフィリナビトラッキングプラグインのプラグイン設定を開き、各種項目を入力します。
6. プラグインを有効にします。
