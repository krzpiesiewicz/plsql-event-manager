import java.time.Instant;
import java.time.LocalDateTime;
import java.time.ZoneId;
import java.time.ZoneOffset;
import java.time.format.DateTimeFormatter;
import java.util.Formatter;
import java.util.Random;

public class Main {

	public static String access(int num) {
		switch(num) {
		case 0: return "denied";
		case 1: return "write";
		case 2: return "read";
		default: return "owner";
		}
	}
	public static void print_add_event(String name, LocalDateTime beginDate, LocalDateTime endDate,
			int defaultAccess, int owner) {
		DateTimeFormatter formatter = DateTimeFormatter.ofPattern("dd.MM.yyyy, HH:mm:ss");
		
		System.out.println("x := ADD_EVENT('" + name +
				"', TO_TIMESTAMP('" + beginDate.format(formatter) + "', 'DD.MM.YYYY, HH24:MI:SS'), " +
			"TO_TIMESTAMP('" + endDate.format(formatter) + "', 'DD.MM.YYYY, HH24:MI:SS'), '" +
			access(defaultAccess) + "', " + owner + ");");
	}
	
	static String names[] = {"Jacka", "Macka", "Ani", "Zuzi", "Krzyska", "Tomka"};
	
	public static void main(String[] args) {
		Random random = new Random(22342342);
		
		
		System.out.println("DECLARE\nx object.id%type;\nBEGIN");
		
		for (int i = 0; i < 1000; i++) {
			String name = "Impreza u " + names[random.nextInt(names.length)];
			
			long minDay = LocalDateTime.of(2018, 1, 30, 0, 0, 0).toInstant(ZoneOffset.UTC).toEpochMilli();
			long maxDay = LocalDateTime.of(2018, 2, 20, 0, 0, 0).toInstant(ZoneOffset.UTC).toEpochMilli();
			long randomDay = minDay + Math.abs(random.nextLong() % (maxDay - minDay));

			long begin = minDay + Math.abs(random.nextLong() % (maxDay - minDay));
			
			long end = minDay + Math.abs(random.nextLong() % (maxDay - minDay));
			
			if (end < begin) {
				long z = begin;
				begin = end;
				end = z;
			}
			
			LocalDateTime beginDate = LocalDateTime.ofInstant(Instant.ofEpochMilli(begin), ZoneId.systemDefault());
//			LocalDateTime endDate = LocalDateTime.ofInstant(Instant.ofEpochMilli(end), ZoneId.systemDefault());
			LocalDateTime endDate = beginDate.plusDays(1);
			
			int defaultAccess = random.nextInt(3);
			
			print_add_event(name, beginDate, endDate, defaultAccess, 63);
		}
		
		LocalDateTime day = LocalDateTime.of(2017, 12, 1, 0, 0, 0);
		LocalDateTime maxDay = LocalDateTime.of(2019, 12, 31, 0, 0, 0);
		
		while(day.isBefore(maxDay)) {
			String name = "Dzien " + day.getDayOfMonth() + "." + day.getMonthValue();
				
			LocalDateTime beginDate = day;
//			LocalDateTime endDate = LocalDateTime.ofInstant(Instant.ofEpochMilli(end), ZoneId.systemDefault());
			LocalDateTime endDate = day.plusDays(1).minusSeconds(1);
			
			int defaultAccess = 2;
			
			print_add_event(name, beginDate, endDate, defaultAccess, 64);
			day = day.plusDays(1);
		}
		
		System.out.println("END;\n/\nCOMMIT;");
	}
}