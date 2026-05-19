import { Container, SectionWrapper, Card, Badge } from "@/components/ui";

const mockEvents = [
  { id: 1, title: "OKR Workshop Istanbul", date: "2024-04-15", location: "Istanbul, Turkey", description: "A hands-on workshop for implementing OKR in your organization." },
  { id: 2, title: "Culture Transformation Summit", date: "2024-05-20", location: "London, UK", description: "Join industry leaders discussing the future of organizational culture." },
];

export default function EventsPage() {
  return (
    <SectionWrapper background="white" className="bg-[linear-gradient(180deg,#fbfcff,#eef3ff)]">
      <Container>
        <h1 className="text-5xl md:text-7xl font-black mb-4 tracking-tight">Events</h1>
        <p className="text-text-secondary mb-10 text-lg">Upcoming workshops, seminars, and conferences.</p>

        {mockEvents.length === 0 ? (
          <p className="text-text-secondary text-center py-16">No upcoming events at this time.</p>
        ) : (
          <div className="space-y-6">
            {mockEvents.map((event) => (
              <Card key={event.id} hoverable>
                <div className="flex flex-col md:flex-row md:items-center gap-5">
                  <div className="flex-shrink-0 w-20 h-20 bg-gradient-to-br from-primary/15 to-accent/15 rounded-2xl flex flex-col items-center justify-center">
                    <span className="text-2xl font-black text-primary">
                      {new Date(event.date).getDate()}
                    </span>
                    <span className="text-xs text-primary uppercase font-bold">
                      {new Date(event.date).toLocaleString("en", { month: "short" })}
                    </span>
                  </div>
                  <div className="flex-1">
                    <h2 className="text-xl font-black">{event.title}</h2>
                    <p className="text-sm text-text-secondary mt-1 leading-6">{event.description}</p>
                    <div className="flex gap-3 mt-3">
                      <Badge variant="neutral">{event.location}</Badge>
                    </div>
                  </div>
                </div>
              </Card>
            ))}
          </div>
        )}
      </Container>
    </SectionWrapper>
  );
}
