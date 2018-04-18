let torrentStream = require('torrent-stream');
let mysql = require('mysql');

var subtitles = require("./subtitles.js");

let times = 0;

if (process.argv[2] == undefined || process.argv[3] == undefined)
{
	console.log("error");
}
else
{
	let firstPiece;
	let lastPiece;
	let got = 0;
	let old = 0;
	let hash = process.argv[2];

	var con = mysql.createConnection({
		host: "127.0.0.1",
		user: "root",
		password: "root",
		database: "hypertube"
	});

	con.connect(function (err) {
		if (err) throw err;
	});

	let magnet = 'magnet:?xt=urn:btih:' + process.argv[2];
	let engine = torrentStream(magnet, {path: '../films'});
	engine.on('ready', function() {
		engine.files.forEach(function(file) {
			if ((file.name.substr(file.name.length - 3) == 'mkv' || file.name.substr(file.name.length - 3) == 'mp4' ||
				file.name.substr(file.name.length - 3) == 'avi') && times == 0 && file.name.toLowerCase().substr(0 , 6) != "sample")
			{
				var d = new Date();
    			var n = d.getTime();
    			n = Math.floor(n / 1000);

				let sql = "INSERT INTO hash (hash, path, downloaded, date) VALUES ?";
				let values = [
					[hash, file.path, 0, n]
				];
				con.query(sql, [values], function (err, result) {
					if (err) throw err;
				});
				let stream = file.createReadStream();

				var fileStart = file.offset;
				var fileEnd = file.offset + file.length;

				var pieceLength = engine.torrent.pieceLength;

				firstPiece = Math.floor(fileStart / pieceLength);
				lastPiece = Math.floor((fileEnd - 1) / pieceLength);
				times = 1;
				var path = file.path;
				path = path.split('/')[0];
				subtitles.dlSubtitles(process.argv[2], process.argv[3], path, file.path, file.name);
			}
		});
	});
	engine.on('download', function(data) {
		if (data >= firstPiece && data <= lastPiece)
		{
			got++;
			let percent = (got / (lastPiece + 1)) * 100;
			percent = Math.round(percent);
			console.log(percent + "%");
			if (percent >= old + 1)
			{
				let sql = "UPDATE hash SET downloaded = ? WHERE hash = ?";
				con.query(sql, [percent, hash], function (err, result) {
					if (err) throw err;
				});
				old = percent;
			}
		}
	});
	engine.on('idle', function() {
		console.log('torrent end');
	});
}
