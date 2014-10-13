/**
 * Created by Николай_2 on 09.10.2014.
 */
package hackazon.image {

import flash.utils.ByteArray;
import mx.core.ByteArrayAsset;
import org.bytearray.gif.player.GIFPlayer;

public class GifLoader {

    public static function getGif(name:Class):GIFPlayer {
        var icon:ByteArrayAsset = new name;
        var buffer:ByteArray = new ByteArray();
        icon.readBytes(buffer);

        var myGIFPlayer:GIFPlayer = new GIFPlayer();
        myGIFPlayer.loadBytes(buffer);

        return myGIFPlayer;
    }

    public static function getLoaderIcon():GIFPlayer {
        return getGif(Resources.LOADING_ICON);
    }
}
}
