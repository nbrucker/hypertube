const OS = require('opensubtitles-api');
const OpenSubtitles = new OS({useragent:'TemporaryUserAgent'});
var http = require('http');
var fs = require('fs');
var srt2vtt = require('srt-to-vtt')

var lang = ['fre', 'eng'];

exports.dlSubtitles = function(hash, imdb, path, file, name) {
	OpenSubtitles.search({
		sublanguageid: lang.join(),
		hash: hash,
		path: file,
		filename: name,
		extensions: ['srt'],
		imdbid: imdb,
	}).then(subtitles => {
		if (subtitles['en'] && !fs.existsSync("../films/" + path + "/en.srt") && !fs.existsSync("../films/" + path + "/en.vtt"))
		{
			var fileen = fs.createWriteStream("../films/" + path + "/en.srt");
			var requesten = http.get(subtitles['en']['url'], function(response) {
				response.pipe(fileen);
				fileen.on('finish', function() {
					fileen.close();
					fs.createReadStream("../films/" + path + "/en.srt")
						.pipe(srt2vtt())
						.pipe(fs.createWriteStream("../films/" + path + "/en.vtt"))
				});
			});
		}
		if (subtitles['fr'] && !fs.existsSync("../films/" + path + "/fr.srt") && !fs.existsSync("../films/" + path + "/fr.vtt"))
		{
			filefr = fs.createWriteStream("../films/" + path + "/fr.srt");
			requestfr = http.get(subtitles['fr']['url'], function(response) {
				response.pipe(filefr);
				filefr.on('finish', function() {
					filefr.close();
					fs.createReadStream("../films/" + path + "/fr.srt")
						.pipe(srt2vtt())
						.pipe(fs.createWriteStream("../films/" + path + "/fr.vtt"))
				});
			});
		}
	});
}
